/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2015
 * @version   3.0.1
 *
 * jQuery methods library for yii2-grid expand row column
 *
 * Author: Kartik Visweswaran
 * Copyright: 2015, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
var kvRowNum = 0, kvExpandRow;
kvExpandRow = function (options) {
    "use strict";
    (function ($) {
        var gridId = options.gridId,
            hiddenFromExport = options.hiddenFromExport,
            detailUrl = options.detailUrl,
            onDetailLoaded = options.onDetailLoaded,
            batchToggle = options.batchToggle,
            expandIcon = options.expandIcon,
            collapseIcon = options.collapseIcon,
            expandTitle = options.expandTitle,
            collapseTitle = options.collapseTitle,
            expandAllTitle = options.expandAllTitle,
            collapseAllTitle = options.collapseAllTitle,
            rowCssClass = hiddenFromExport ? options.rowCssClass + ' skip-export' : options.rowCssClass,
            duration = options.animationDuration,
            $grid = $('#' + gridId),
            $hdrCell = $grid.find('.kv-expand-header-cell'),
            $hdrIcon = $hdrCell.find('.kv-expand-header-icon'),
            collapseAll = options.collapseAll === undefined ? false : options.collapseAll,
            expandAll = options.expandAll === undefined ? false : options.expandAll,
            $rows = $grid.find("td:visible .kv-expand-row:not(.kv-state-disabled)"),
            numRows = $rows.length, progress = 'kv-expand-detail-loading',
            isExpanded = function ($i) {
                return $i.hasClass('kv-state-collapsed') && !$i.hasClass('kv-state-disabled');
            },
            isCollapsed = function ($i) {
                return $i.hasClass('kv-state-expanded') && !$i.hasClass('kv-state-disabled');
            },
            setCss = function ($el, css) {
                $el.removeClass(css).addClass(css);
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
                var delay = isNaN(duration) ? 1000 : duration + 200;
                setTimeout(function () {
                    $c.removeClass(progress);
                }, delay);
            };
        if ($rows.length === 0) {
            setCss($hdrCell, 'kv-state-disabled');
            return;
        }
        $rows.each(function () {
            var $el = $(this), $newRow, $tr,
                $icon = $el.find('.kv-expand-icon'),
                $row = $el.closest('tr'),
                $cell = $el.closest('.kv-expand-icon-cell'),
                $container = $el.find('.kv-expand-detail'),
                $detail = $el.find('.kv-expanded-row'),
                vKey = $detail.data('key'),
                vInd = $detail.data('index'),
                cols = $row.find('td:visible').length,
                params = {};

            if (!isExpanded($icon) && !isCollapsed($icon)) {
                return true;
            }
            if ($detail.length === 0) {
                vKey = $row.data('key');
                $newRow = $row.next('tr.kv-expand-detail-row[data-key="' + vKey + '"]');
                $detail = $newRow.find('.kv-expanded-row');
            }
            var loadDetail = function (postProcess) {
                    beginLoading($cell);
                    if (detailUrl.length > 0 && $detail.html().length === 0) {
                        $grid.trigger('kvexprow.beforeLoad', [vInd, vKey]);

                        var detailData = $detail.data();
                        for(var p in detailData) {
                            if(detailData.hasOwnProperty(p) && p.match(/param/)) {
                                params[p.replace(/param/, '').toLowerCase()] = detailData[p];
                            }
                        }

                        $detail.load(detailUrl, $.extend({}, {
                            expandRowKey: vKey,
                            expandRowInd: vInd
                        }, params), function () {
                            endLoading($cell);
                            if (onDetailLoaded && $.isFunction(onDetailLoaded)) {
                                onDetailLoaded();
                            }
                            postProcess();
                            $grid.trigger('kvexprow.loaded', [vInd, vKey]);
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
                    var newRow = '<tr class="kv-expand-detail-row ' + rowCssClass + '" ' +
                        'data-key="' + vKey + '" ' +
                        'data-index="' + vInd + '">';
                    $detail.wrap('<td colspan="' + cols + '">').parent().wrap(newRow);
                    $icon.html(collapseIcon);
                    $cell.attr('title', collapseTitle);
                    if (animate) {
                        $detail.slideDown(duration, function () {
                            setCollapsed($icon);
                        });
                    } else {
                        $detail.show();
                        setCollapsed($icon);
                    }
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
                    });
                    endLoading($cell);
                };
            if (expandAll && batchToggle) {
                if (isCollapsed($icon)) {
                    loadDetail(function () {
                        expandRow(true);
                        kvRowNum++;
                        if (kvRowNum >= numRows) {
                            endLoading($hdrCell);
                            $hdrIcon.focus();
                        }
                    });
                }
                if (kvRowNum >= numRows) {
                    endLoading($hdrCell);
                    $hdrIcon.focus();
                }
                return true;
            }
            if (collapseAll && batchToggle) {
                if (isExpanded($icon)) {
                    collapseRow();
                    kvRowNum++;
                    if (kvRowNum >= numRows) {
                        endLoading($hdrCell);
                        $hdrIcon.focus();
                    }
                }
                if (kvRowNum >= numRows) {
                    endLoading($hdrCell);
                    $hdrIcon.focus();
                }
                return true;
            }
            if (isExpanded($icon)) {
                expandRow(false);
            }
            $cell.off().on('click', function () {
                if ($cell.hasClass(progress)) {
                    return;
                }
                if (isCollapsed($icon)) {
                    loadDetail(function () {
                        expandRow(true);
                    });
                    $grid.trigger('kvexprow.toggle', [vInd, vKey, true]);
                    $icon.focus();
                    return;
                }
                if (isExpanded($icon)) {
                    collapseRow();
                    $grid.trigger('kvexprow.toggle', [vInd, vKey, false]);
                    $icon.focus();
                }
            });
        });
        if (!batchToggle) {
            return;
        }
        $hdrCell.off().on('click', function () {
            if ($hdrCell.hasClass(progress) || $rows.length === 0) {
                return;
            }
            var collAll = isCollapsed($hdrIcon), expAll = isExpanded($hdrIcon),
                opt = $.extend({}, options, {expandAll: expAll, collapseAll: collAll});
            beginLoading($hdrCell);
            if (expAll) {
                kvRowNum = $rows.find(".kv-state-collapsed").length;
                setExpanded($hdrIcon);
                $hdrIcon.html(collapseIcon);
                $hdrCell.attr('title', collapseAllTitle);
                $grid.trigger('kvexprow.toggleAll', [false]);
            } else {
                if (collAll) {
                    kvRowNum = $rows.find(".kv-state-expanded").length;
                    setCollapsed($hdrIcon);
                    $hdrIcon.html(expandIcon);
                    $hdrCell.attr('title', expandAllTitle);
                    $grid.trigger('kvexprow.toggleAll', [true]);
                }
            }
            kvExpandRow(opt);
        });
    })(window.jQuery);
};