/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2023
 * @version   3.5.3
 *
 * jQuery methods library for yii2-grid expand row column
 *
 * Author: Kartik Visweswaran
 * Copyright: 2014 - 2023, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
var kvExpandRow;
(function ($) {
    'use strict';
    var NS = '.kvExpandRowColumn', $h = {
        isExpanded: function ($i) {
            return $i.hasClass('kv-state-expanded') && !$i.hasClass('kv-state-disabled');
        },
        isCollapsed: function ($i) {
            return $i.hasClass('kv-state-collapsed') && !$i.hasClass('kv-state-disabled');
        },
        setCss: function ($el, css) {
            if ($el.length) {
                $el.removeClass(css).addClass(css);
            }
        },
        setExpanded: function ($i) {
            $i.removeClass('kv-state-expanded').addClass('kv-state-collapsed');
        },
        setCollapsed: function ($i) {
            $i.removeClass('kv-state-collapsed').addClass('kv-state-expanded');
        },
        handler: function ($el, event, callback, skipNS) {
            var ev = skipNS ? event : event.split(' ').join(NS + ' ') + NS;
            if ($el.length) {
                $el.off(ev).on(ev, callback);
            }
        }
    };
    kvExpandRow = function (options, id) {
        //noinspection JSUnresolvedVariable
        var ToggleManager, kvRowNumVar = 'kvRowNum_' + id, gridId = options.gridId,
            hiddenFromExport = options.hiddenFromExport, detailUrl = options.detailUrl,
            onDetailLoaded = options.onDetailLoaded, expandIcon = options.expandIcon,
            collapseIcon = options.collapseIcon, expandTitle = options.expandTitle,
            collapseTitle = options.collapseTitle, expandAllTitle = options.expandAllTitle,
            collapseAllTitle = options.collapseAllTitle, expandOneOnly = options.expandOneOnly,
            enableRowClick = options.enableRowClick, rowClickExcludedTags = options.rowClickExcludedTags,
            enableCache = options.enableCache, extraData = options.extraData,
            msgDetailLoading = options.msgDetailLoading,
            rowCssClass = hiddenFromExport ? options.rowCssClass + ' skip-export' : options.rowCssClass,
            duration = options.animationDuration, $grid = $('#' + gridId), idCss = '.' + id,
            $hdrCell = $grid.find('.kv-expand-header-cell.kv-batch-toggle' + idCss),
            $hdrIcon = $hdrCell.find('.kv-expand-header-icon'),
            collapseAll = options.collapseAll === undefined ? false : options.collapseAll,
            expandAll = options.expandAll === undefined ? false : options.expandAll,
            $cells = $grid.find('td.kv-expand-icon-cell' + idCss + ' .kv-expand-row:not(.kv-state-disabled)'),
            numRows = $cells.length, progress = 'kv-expand-detail-loading',
            getCols = function () {
                var $col = $grid.find('td.kv-expand-icon-cell' + idCss + ':first'),
                    $row = $col && $col.length ? $col.closest('tr') : '', cols = 0;
                if (!$row || !$row.length) {
                    return 0;
                }
                $row.find('> td').each(function () {
                    if ($(this).css('display') !== 'none') {
                        cols++;
                    }
                });
                return cols;
            },
            cols = getCols(),
            beginLoading = function ($c) {
                $h.setCss($c, progress);
            },
            endLoading = function ($c) {
                if (!$c.length) {
                    return;
                }
                var delay = isNaN(duration) ? 1000 : duration + 200;
                setTimeout(function () {
                    $c.removeClass(progress);
                }, delay);
            },
            getRowNum = function () {
                var num = $grid.data(kvRowNumVar);
                num = num ? parseInt(num) : 0;
                return isNaN(num) ? 0 : num;
            },
            setRowNum = function (value) {
                $grid.data(kvRowNumVar, value);
            },
            incrementRowNum = function () {
                var num = getRowNum();
                $grid.data(kvRowNumVar, num + 1);
            },
            expandHeaderCell = function () {
                setRowNum($cells.find('.kv-state-expanded').length);
                $h.setExpanded($hdrIcon);
                $hdrIcon.html(collapseIcon);
                $hdrCell.attr('title', collapseAllTitle);
            },
            collapseHeaderCell = function () {
                setRowNum($cells.find('.kv-state-collapsed').length);
                $h.setCollapsed($hdrIcon);
                $hdrIcon.html(expandIcon);
                $hdrCell.attr('title', expandAllTitle);
            },
            initExpandRowColumn = function () {
                var expandedCells = 0, collapsedCells = 0, totalCells = $cells.length;
                // initialize expanded cells content
                if (!$grid.data(kvRowNumVar)) {
                    setRowNum(0);
                }
                if (extraData.length === 0) {
                    extraData = {};
                }
                if (totalCells === 0) {
                    $h.setCss($hdrCell, 'kv-state-disabled');
                    return;
                }
                $cells.each(function () {
                    var $cell = $(this), manager = new ToggleManager($cell), $icon = $cell.find('>.kv-expand-icon');
                    if ($icon.hasClass('kv-state-init-expanded')) {
                        manager.expand(false);
                        expandedCells++;
                    } else if ($icon.hasClass('kv-state-init-collapsed')) {
                        manager.collapse(false);
                        collapsedCells++;
                    }
                    $icon.removeClass('kv-state-init-expanded kv-state-init-collapsed');
                    manager.run();
                });
                if (!$hdrCell.length) {
                    return;
                }
                if (totalCells === expandedCells) {
                    expandHeaderCell();
                } else if (totalCells === collapsedCells) {
                    collapseHeaderCell();
                }
                $h.handler($hdrCell, 'click', function () {
                    if ($hdrCell.hasClass(progress) || $cells.length === 0) {
                        return;
                    }
                    var collAll = $h.isCollapsed($hdrIcon), expAll = $h.isExpanded($hdrIcon),
                        opt = $.extend(true, {}, options, {expandAll: expAll, collapseAll: collAll});
                    beginLoading($hdrCell);
                    if (expAll) {
                        expandHeaderCell();
                        $grid.trigger('kvexprow:toggleAll', [extraData, false]);
                    } else if (collAll) {
                        collapseHeaderCell();
                        $grid.trigger('kvexprow:toggleAll', [extraData, true]);
                    }
                    kvExpandRow(opt, id);
                });
            };
        ToggleManager = function ($element) {
            var self = this;
            self.$element = $element;
            self.init();
        };
        ToggleManager.prototype = {
            constructor: ToggleManager,
            init: function () {
                var self = this, $newRow;
                self.$row = self.$element.closest('tr');
                self.$icon = self.$element.find('>.kv-expand-icon');
                self.$detail = self.$element.find('.kv-expanded-row' + idCss + ':first');
                self.$cell = self.$icon.closest('.kv-expand-icon-cell');
                self.$container = self.$cell.find('.kv-expand-detail:first');
                self.vKey = self.$detail.data('key');
                self.vInd = self.$detail.data('index');
                if (self.$detail.length === 0) {
                    self.vKey = self.$row.data('key');
                    $newRow = self.$row.next('tr.kv-expand-detail-row[data-key="' + self.vKey + '"]');
                    self.$detail = $newRow.find('.kv-expanded-row');
                }
            },
            run: function () {
                var self = this, $row = self.$row, $cell = self.$cell, $icon = self.$icon;
                if (expandAll) {
                    if ($h.isCollapsed($icon)) {
                        self.load(function () {
                            self.expand(true);
                            incrementRowNum();
                            if (getRowNum() >= numRows) {
                                endLoading($hdrCell);
                                $hdrIcon.focus();
                            }
                        });
                    }
                    if (getRowNum() >= numRows) {
                        endLoading($hdrCell);
                        $hdrIcon.focus();
                    }
                    return;
                }
                if (collapseAll) {
                    if ($h.isExpanded($icon)) {
                        self.collapse();
                        incrementRowNum();
                        if (getRowNum() >= numRows) {
                            endLoading($hdrCell);
                            $hdrIcon.focus();
                        }
                    }
                    if (getRowNum() >= numRows) {
                        endLoading($hdrCell);
                        $hdrIcon.focus();
                    }
                    return;
                }
                if ($h.isExpanded($icon)) {
                    if (detailUrl) {
                        self.load(function () {
                            self.expand(false);
                        });
                    } else {
                        self.expand(false);
                    }
                }
                $h.handler($cell, 'click', function (event) {
                    if (!$icon.hasClass('kv-state-disabled')) {
                        self.toggle();
                    }
                    event.stopPropagation();
                });
                $h.handler($row, 'click', function (event) {
                    var target = event.target, clickDisabled = $(target).length &&
                        $(target).hasClass('kv-disable-click') ||
                        $.inArray(target.nodeName, rowClickExcludedTags) !== -1;
                    if (enableRowClick && !clickDisabled) {
                        self.toggle();
                    }
                });
            },
            load: function (postProcess) {
                var self = this, $cell = self.$cell, $detail = self.$detail, vKey = self.vKey, vInd = self.vInd,
                    params = $.extend({expandRowKey: vKey, expandRowInd: vInd}, extraData),
                    reload = enableCache ? $detail.html().length === 0 : true;
                if (detailUrl.length > 0 && reload) {
                    $.ajax({
                        type: 'POST',
                        data: params,
                        url: detailUrl,
                        beforeSend: function () {
                            beginLoading($cell);
                            $grid.trigger('kvexprow:beforeLoad', [vInd, vKey, extraData]);
                            $detail.html(msgDetailLoading);
                        },
                        success: function (out) {
                            $detail.html(out);
                            endLoading($cell);
                            if (typeof onDetailLoaded === 'function') {
                                onDetailLoaded();
                            }
                            postProcess();
                            $grid.trigger('kvexprow:loaded', [vInd, vKey, extraData]);
                        },
                        error: function () {
                            $detail.html(
                                '<div class="alert alert-danger">Error fetching data. Please try again later.</div>');
                            $grid.trigger('kvexprow:error', [vInd, vKey, extraData]);
                            endLoading($cell);
                        }
                    });
                    return;
                } else {
                    endLoading($cell);
                }
                if (typeof postProcess === 'function') {
                    postProcess();
                }
            },
            expand: function (animate) {
                var self = this, $row = self.$row, $icon = self.$icon, $cell = self.$cell,
                    $detail = self.$detail, vKey = self.vKey, vInd = self.vInd, isAjax = detailUrl.length > 0;
                if ($h.isExpanded($icon)) {
                    return;
                }
                if (!isAjax) {
                    beginLoading($cell);
                }
                $grid.find('tr[data-index="' + vInd + '"]').remove();
                $detail.hide();
                $row.after($detail);
                var newRow = '<tr class="kv-expand-detail-row ' + rowCssClass + '" data-key="' + vKey +
                    '" data-index="' + vInd + '">';
                //noinspection JSValidateTypes
                $detail.wrap('<td colspan="' + cols + '">').parent().wrap(newRow);
                $icon.html(collapseIcon);
                $cell.attr('title', collapseTitle);
                if (animate) {
                    $detail.slideDown(duration, function () {
                        $h.setCollapsed($icon);
                        $detail.show();
                    });
                } else {
                    $detail.show();
                    $h.setCollapsed($icon);
                }
                // needed when used together with grouping
                if ($row.attr('data-group-key')) {
                    var $rowsBefore = $row.prevAll('.' + gridId), expandRowPosition = $row.index() + 1;
                    $rowsBefore.push($row);
                    $.each($rowsBefore, function (i, tr) {
                        var $rowSpanTds = $(tr).find('td.' + gridId + '[rowspan]');
                        $.each($rowSpanTds, function (j, td) {
                            var rowSpan = parseInt($(td).attr('rowspan'));
                            if (!isNaN(rowspan) && $(tr).index() + rowSpan > expandRowPosition) {
                                $(td).attr('rowspan', rowSpan + 1);
                            }
                        });
                    });
                }
                if (!isAjax) {
                    endLoading($cell);
                }
            },
            collapse: function (hideProgress) {
                var self = this, $row = self.$row, $icon = self.$icon, $cell = self.$cell, $detail = self.$detail,
                    $container = self.$container;
                if ($h.isCollapsed($icon)) {
                    return;
                }
                if (!hideProgress) {
                    beginLoading($cell);
                }
                $container.html('');
                $icon.html(expandIcon);
                $cell.attr('title', expandTitle);
                $detail.slideUp(duration, function () {
                    var $detailRow = $detail.closest('tr.kv-expand-detail-row');
                    if ($detailRow.length) {
                        $detailRow.before($detail).remove();
                    }
                    $detail.appendTo($container);
                    $h.setExpanded($icon);
                    // needed when used together with grouping
                    if ($row.attr('data-group-key')) {
                        var $rowsBefore = $row.prevAll('.' + gridId);
                        $rowsBefore.push($row);
                        var expandRowPosition = $row.index() + 1;
                        $.each($rowsBefore, function (i, tr) {
                            var $rowSpanTds = $(tr).find('td.' + gridId + '[rowspan]');
                            $.each($rowSpanTds, function (j, td) {
                                var rowSpan = parseInt($(td).attr('rowspan'));
                                if (!isNaN(rowspan) && $(tr).index() + rowSpan > expandRowPosition) {
                                    $(td).attr('rowspan', rowSpan - 1);
                                }
                            });
                        });
                    }
                });
                if (!hideProgress) {
                    endLoading($cell);
                }
            },
            toggle: function () {
                var self = this, $icon = self.$icon, $cell = self.$cell, chk, collapsed = false,
                    vKey = self.vKey, vInd = self.vInd;
                if ($cell.hasClass(progress)) {
                    return;
                }
                if ($h.isCollapsed($icon)) {
                    chk = expandOneOnly && !collapseAll;
                    if (chk) {
                        $cells.each(function () {
                            var manager = new ToggleManager($(this));
                            manager.collapse(true);
                        });
                        collapsed = true;
                    }
                    self.load(function () {
                        self.expand(true);
                    });
                    if (!chk || collapsed) {
                        $grid.trigger('kvexprow:toggle', [vInd, vKey, extraData, true]);
                        $icon.focus();
                    }
                    return;
                }
                if ($h.isExpanded($icon)) {
                    self.collapse();
                    $grid.trigger('kvexprow:toggle', [vInd, vKey, extraData, false]);
                    $icon.focus();
                }
            }
        };
        initExpandRowColumn();
    };
})(window.jQuery);