/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   3.3.4
 *
 * jQuery methods library for yii2-grid expand row column
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2014 - 2019, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
var kvExpandRow;
(function ($) {
    "use strict";
    kvExpandRow = function (options, id) {
        //noinspection JSUnresolvedVariable
        var NS = '.kvExpandRowColumn',
            kvRowNumVar = 'kvRowNum_' + id,
            gridId = options.gridId,
            hiddenFromExport = options.hiddenFromExport,
            detailUrl = options.detailUrl,
            onDetailLoaded = options.onDetailLoaded,
            expandIcon = options.expandIcon,
            collapseIcon = options.collapseIcon,
            expandTitle = options.expandTitle,
            collapseTitle = options.collapseTitle,
            expandAllTitle = options.expandAllTitle,
            collapseAllTitle = options.collapseAllTitle,
            expandOneOnly = options.expandOneOnly,
            enableRowClick = options.enableRowClick,
            rowClickExcludedTags = options.rowClickExcludedTags,
            enableCache = options.enableCache,
            extraData = options.extraData,
            rowCssClass = hiddenFromExport ? options.rowCssClass + ' skip-export' : options.rowCssClass,
            duration = options.animationDuration,
            $grid = $('#' + gridId),
            idCss = '.' + id,
            $hdrCell = $grid.find('.kv-expand-header-cell.kv-batch-toggle' + idCss),
            $hdrIcon = $hdrCell.find('.kv-expand-header-icon'),
            collapseAll = options.collapseAll === undefined ? false : options.collapseAll,
            expandAll = options.expandAll === undefined ? false : options.expandAll,
            $rows = $grid.find('td.kv-expand-icon-cell' + idCss + ' .kv-expand-row:not(.kv-state-disabled)'),
            numRows = $rows.length, progress = 'kv-expand-detail-loading',
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
            isExpanded = function ($i) {
                return $i.hasClass('kv-state-collapsed') && !$i.hasClass('kv-state-disabled');
            },
            isCollapsed = function ($i) {
                return $i.hasClass('kv-state-expanded') && !$i.hasClass('kv-state-disabled');
            },
            setCss = function ($el, css) {
                if ($el.length) {
                    $el.removeClass(css).addClass(css);
                }
            },
            setExpanded = function ($i) {
                $i.removeClass('kv-state-collapsed').addClass('kv-state-expanded');
            },
            setCollapsed = function ($i) {
                $i.removeClass('kv-state-expanded').addClass('kv-state-collapsed');
            },
            beginLoading = function ($c) {
                setCss($c, progress);
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
            getRowNum = function() {
                var num = $grid.data(kvRowNumVar);
                num = num ? parseInt(num) : 0;
                return isNaN(num) ? 0 : num;
            },
            setRowNum = function(value) {
                $grid.data(kvRowNumVar, value);
            },
            incrementRowNum = function() {
                var num = getRowNum();
                $grid.data(kvRowNumVar, num + 1);
            },
            handler = function ($el, event, callback, skipNS) {
                var ev = skipNS ? event : event.split(' ').join(NS + ' ') + NS;
                if ($el.length) {
                    $el.off(ev).on(ev, callback);
                }
            };
        if (!$grid.data(kvRowNumVar)) {
            setRowNum(0);
        }
        if (extraData.length === 0) {
            extraData = {};
        }
        if ($rows.length === 0) {
            setCss($hdrCell, 'kv-state-disabled');
            return;
        }
        $rows.each(function () {
            var $el = $(this), $newRow, $tr, $icon = $el.find('>.kv-expand-icon'),
                $cell = $icon.closest('.kv-expand-icon-cell'),
                $row = $el.closest('tr'), $container = $cell.find('.kv-expand-detail'),
                $detail = $el.find('.kv-expanded-row' + idCss + ':first'),
                vKey = $detail.data('key'), vInd = $detail.data('index');
            if (!isExpanded($icon) && !isCollapsed($icon)) {
                return true;
            }
            if ($detail.length === 0) {
                vKey = $row.data('key');
                $newRow = $row.next('tr.kv-expand-detail-row[data-key="' + vKey + '"]');
                $detail = $newRow.find('.kv-expanded-row');
            }
            var loadDetail = function (postProcess) {
                    var params = $.extend({
                            expandRowKey: vKey,
                            expandRowInd: vInd
                        }, extraData),
                        reload = enableCache ? $detail.html().length === 0 : true;
                    if (detailUrl.length > 0 && reload) {
                        $.ajax({
                            type: 'POST',
                            data: params,
                            url: detailUrl,
                            beforeSend: function() {
                                beginLoading($cell);
                                $grid.trigger('kvexprow:beforeLoad', [vInd, vKey, extraData]);
                            },
                            success: function(out) {
                                $detail.html(out);
                                endLoading($cell);
                                if (typeof onDetailLoaded === 'function') {
                                    onDetailLoaded();
                                }
                                postProcess();
                                $grid.trigger('kvexprow:loaded', [vInd, vKey, extraData]);
                            },
                            error: function() {
                                $detail.html('<div class="alert alert-danger">Error fetching data. Please try again later.</div>');
                                $grid.trigger('kvexprow:error', [vInd, vKey, extraData]);
                                endLoading($cell);
                            }
                        });
                        return;
                    } else {
                        endLoading($cell);
                    }
                    postProcess();
                },
                expandRow = function (animate) {
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
                            setCollapsed($icon);
                            $detail.show();
                        });
                    } else {
                        $detail.show();
                        setCollapsed($icon);
                    }
                    // needed when used together with grouping
                    var $rowsBefore = $row.prevAll(), expandRowPosition = $row.index() + 1;
                    $rowsBefore.push($row);
                    $.each($rowsBefore, function (i, tr) {
                        var $rowSpanTds = $(tr).find('td[rowspan]');
                        $.each($rowSpanTds, function(j, td) {
                            var rowSpan = parseInt($(td).attr('rowspan'));
                            if ($(tr).index() + rowSpan > expandRowPosition) {
                                $(td).attr('rowspan', rowSpan + 1);
                            }
                        });
                    });
                    if (detailUrl.length === 0) {
                        endLoading($cell);
                    }
                },
                collapseRow = function () {
                    beginLoading($cell);
                    $container.html('');
                    $icon.html(expandIcon);
                    $cell.attr('title', expandTitle);
                    $tr = $detail.closest('.kv-expand-detail-row');
                    $detail.slideUp(duration, function () {
                        $detail.unwrap().unwrap();
                        $detail.appendTo($container);
                        setExpanded($icon);
                        // needed when used together with grouping
                        var $rowsBefore = $row.prevAll();
                        $rowsBefore.push($row);
                        var expandRowPosition = $row.index() + 1;
                        $.each($rowsBefore, function (i, tr) {
                            var $rowSpanTds = $(tr).find('td[rowspan]');
                            $.each($rowSpanTds, function(j, td) {
                                var rowSpan = parseInt($(td).attr('rowspan'));
                                if ($(tr).index() + rowSpan > expandRowPosition) {
                                    $(td).attr('rowspan', rowSpan - 1);
                                }
                            });
                        });
                    });
                    endLoading($cell);
                },
                toggleRow = function () {
                    var opt, chk, collapsed = false, loading = false;
                    if ($cell.hasClass(progress)) {
                        return;
                    }
                    if (isCollapsed($icon)) {
                        chk = expandOneOnly && !collapseAll;
                        if (chk) {
                            opt = $.extend({}, options, {collapseAll: true});
                            $rows.each(function () {
                                if ($(this).closest('.kv-expand-icon-cell').hasClass(progress)) {
                                    loading = true;
                                    //noinspection UnnecessaryReturnStatementJS
                                    return;
                                }
                            });
                            if (loading) {
                                return;
                            }
                            kvExpandRow(opt);
                            collapsed = true;
                        }
                        loadDetail(function () {
                            expandRow(true);
                        });
                        if (!chk || collapsed) {
                            $grid.trigger('kvexprow:toggle', [vInd, vKey, extraData, true]);
                            $icon.focus();
                        }
                        return;
                    }
                    if (isExpanded($icon)) {
                        collapseRow();
                        $grid.trigger('kvexprow:toggle', [vInd, vKey, extraData, false]);
                        $icon.focus();
                    }
                };
            if (expandAll) {
                if (isCollapsed($icon)) {
                    loadDetail(function () {
                        expandRow(true);
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
                return true;
            }
            if (collapseAll) {
                if (isExpanded($icon)) {
                    collapseRow();
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
                return true;
            }
            if (isExpanded($icon)) {
                if (detailUrl) {
                    loadDetail(function () {
                        expandRow(false);
                    });
                } else {
                    expandRow(false);
                }
            }
            handler($cell, 'click', function (event) {
                toggleRow($cell);
                event.stopPropagation();
            });
            handler($row, 'click', function (event) {
                var target = event.target, clickDisabled = $(target).length &&
                    $(target).hasClass('kv-disable-click') ||
                    $.inArray(target.nodeName, rowClickExcludedTags) !== -1;
                if (enableRowClick && !clickDisabled) {
                    toggleRow($cell);
                }
            });
        });
        if (!$hdrCell.length) {
            return;
        }
        handler($hdrCell, 'click', function () {
            if ($hdrCell.hasClass(progress) || $rows.length === 0) {
                return;
            }
            var collAll = isCollapsed($hdrIcon), expAll = isExpanded($hdrIcon),
                opt = $.extend({}, options, {expandAll: expAll, collapseAll: collAll});
            beginLoading($hdrCell);
            if (expAll) {
                setRowNum($rows.find(".kv-state-collapsed").length);
                setExpanded($hdrIcon);
                $hdrIcon.html(collapseIcon);
                $hdrCell.attr('title', collapseAllTitle);
                $grid.trigger('kvexprow:toggleAll', [extraData, false]);
            } else {
                if (collAll) {
                    setRowNum($rows.find(".kv-state-expanded").length);
                    setCollapsed($hdrIcon);
                    $hdrIcon.html(expandIcon);
                    $hdrCell.attr('title', expandAllTitle);
                    $grid.trigger('kvexprow:toggleAll', [extraData, true]);
                }
            }
            kvExpandRow(opt, id);
        });
    };
})(window.jQuery);