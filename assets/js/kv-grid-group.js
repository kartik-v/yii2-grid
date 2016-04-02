/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2016
 * @version   3.1.1
 *
 * Grid grouping jquery library created for yii2-grid.
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2015, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
var kvGridGroup;
(function ($) {
    "use strict";
    kvGridGroup = function (gridId) {
        var $grid, data, groups, $groupRows, i, n, colCount, $pageSum, $firstRow, $lastRow, isEmpty, initPageSummary,
            formatNumber, applyFormat, getParentGroup, getLastGroupRow, getSummarySource, getSummaryContent, addRowSpan,
            adjustLastRow, createSummary;
        $grid = $('#' + gridId);
        data = {};
        groups = [];
        colCount = 0;
        $pageSum = $grid.find('tr.kv-page-summary');
        $firstRow = $grid.find('tr[data-key]:first');
        $lastRow = $grid.find('tr[data-key]:last');
        isEmpty = function (v) {
            return v === undefined || v === null || v.length === 0;
        };
        initPageSummary = function () {
            var i = 0;
            if (!$pageSum.length) {
                return;
            }
            $pageSum.find('td').each(function () {
                $(this).attr('data-col-seq', i);
                i++;
            });
        };
        formatNumber = function (number, decimals, dec_point, thousands_sep) {
            number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
            var n = !isFinite(+number) ? 0 : +number, s,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
                toFixedFix = function (n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += [(prec - s[1].length + 1)].join('0');
            }
            return s.join(dec);
        };
        applyFormat = function (source, config, $tr, $td, i) {
            var decimals, decPoint, thousandSep, data, func;
            /** @namespace config.thousandSep */
            /** @namespace config.decPoint */
            /** @namespace config.func */
            if (config.format === 'number') {
                decimals = config.decimals || 0;
                decPoint = config.decPoint || '.';
                thousandSep = config.thousandSep || ',';
                return formatNumber(source, decimals, decPoint, thousandSep);
            }
            if (config.format === 'callback') {
                func = window[config.func];
                if (typeof func === 'function') {
                    data = getSummarySource($tr, $td, i);
                    return func(source, data);
                }
            }
            return source;
        };
        getParentGroup = function ($cell) {
            var $tr, $td, id = $cell.attr('data-sub-group-of'), i, tag;
            if (isEmpty(id)) {
                return null;
            }
            tag = 'td[data-col-seq="' + id + '"]';
            $tr = $cell.closest('tr');
            $td = $tr.find(tag);
            i = $td.length;
            if (i > 0) {
                return $td;
            }
            while (i === 0 && $tr.length) {
                $tr = $tr.prev();
                $td = $tr.find(tag);
                i = $td.length;
            }
            return i === 0 ? null : $td;
        };
        getLastGroupRow = function ($cell, $tr) {
            var key = $cell.attr('data-group-key'), i = 0, $endRow = $tr;
            if ($tr.attr('data-last-row')) {
                return $tr;
            }
            while (i === 0 && $endRow.length) {
                $endRow = $tr;
                $tr = $tr.next();
                i = $tr.find('td[data-group-key="' + key + '"]').length;
            }
            return $endRow.length ? $endRow : $lastRow;
        };
        getSummarySource = function ($tr, $td, i) {
            var j = 1, data = [], $row = $tr, isGrouped = $row.hasClass('kv-grid-group-row'),
                rowspan = $td.attr('rowspan') || 1;
            if (isGrouped) {
                j = false;
                $row = $row.next(':not(.kv-grid-group-row');
                while (!j && $row.length) {
                    $row.find('td[data-col-seq="' + i + '"]').each(function () {
                        var out = $(this).text().replace(/[\s,]+/g, '');
                        out = parseFloat(out);
                        data.push(out);
                    }); // jshint ignore:line
                    j = $row.hasClass('kv-grid-group-row');
                    $row = $row.next();
                }
            } else {
                while (j <= rowspan && $row.length) {
                    $row.find('td[data-col-seq="' + i + '"]').each(function () {
                        var out = $(this).text().replace(/[\s,]+/g, '');
                        out = parseFloat(out);
                        data.push(out);
                    }); // jshint ignore:line
                    $row = $row.next();
                    j++;
                }
            }
            return data;
        };
        getSummaryContent = function (source, $tr, $td, i) {
            var func, data, out = 0, n;
            switch (source) {
                case 'f_count':
                case 'f_sum':
                case 'f_avg':
                case 'f_max':
                case 'f_min':
                    data = getSummarySource($tr, $td, i);
                    switch (source) {
                        case 'f_count':
                            return data.length;
                        case 'f_sum':
                        case 'f_avg':
                            $.each(data, function (key, val) {
                                out += val;
                            });
                            if (source === 'f_sum') {
                                return out;
                            }
                            n = data.length;
                            return n ? out / n : out;
                        case 'f_max':
                        case 'f_min':
                            func = source.replace('f_', '');
                            return Math[func].apply(null, data);
                    }
                    return null;
                default:
                    return source;
            }
        };
        addRowSpan = function ($el, n) {
            n = n || 1;
            var rowspan = $el.attr('rowspan') || 1;
            rowspan = parseInt(rowspan) + n;
            $el.attr('rowspan', rowspan);
        };
        adjustLastRow = function () {
            var i, rows = [];
            $lastRow.nextAll('tr.kv-group-footer').each(function () {
                rows.push($(this));
            });
            if (rows.length) {
                for (i = 0; i < rows.length; i++) {
                    $lastRow.after(rows[i]);
                }
            }
            if ($pageSum.length) {
                $pageSum.find('td').each(function () {
                    var $td = $(this);
                    if (!$firstRow.find('td[data-col-seq="' + $td.attr('data-col-seq') + '"]').length) {
                        $td.remove();
                    }
                });
            }
        };
        createSummary = function ($cell, type) {
            var data = $cell.data(type), $parent, key, $tr, $td, i, j, $row, $col, $target, content,
                isGroupedRow = false, css = (type === 'groupHeader') ? 'kv-group-header' : 'kv-group-footer';
            if (!data) {
                return;
            }
            key = $cell.attr('data-group-key');
            $tr = $cell.closest('tr');
            if (key) {
                $tr.attr('data-group-key', key);
            }
            $parent = $cell.attr('data-sub-group-of') ? getParentGroup($cell) : null;
            isGroupedRow = $parent && $parent.length && $parent.is('[data-grouped-row]');
            key = $parent && $parent.length ? $parent.attr('data-col-seq') : null;
            $row = $(document.createElement('tr'));
            if (data.options) {
                $row.attr(data.options).addClass(css);
            }
            $firstRow.find('td').each(function () {
                $td = $(this);
                i = $td.attr('data-col-seq');
                if (!key || i != key || isGroupedRow) { // jshint ignore:line
                    $col = $(document.createElement('td')).attr('data-summary-col-seq', i);
                    if (data.content && data.content[i]) {
                        content = getSummaryContent(data.content[i], $tr, $cell, i);
                        /** @namespace data.contentFormats */
                        /** @namespace data.contentOptions */
                        if (data.contentFormats && data.contentFormats[i]) {
                            content = applyFormat(content, data.contentFormats[i], $tr, $cell, i);
                        }
                        $col.html(content);
                    }
                    if (data.contentOptions && data.contentOptions[i]) {
                        $col.attr(data.contentOptions[i]);
                    }
                    $col.appendTo($row);
                }
            });
            if ($parent && $parent.length && !isGroupedRow) {
                addRowSpan($parent);
            }
            if (type === 'groupHeader') {
                $tr.before($row);
                if ($tr.find('td[data-col-seq="' + key + '"]').length) {
                    $row.find('td').each(function () {
                        var $td = $(this), seq = parseInt($td.attr('data-summary-col-seq'));
                        key = parseInt(key);
                        if (seq === key - 1) {
                            $td.after($parent);
                            return;
                        }
                        if (seq === key + 1) {
                            $td.before($parent);
                            return;
                        }
                        if (seq > key) {
                            $td.before($parent);
                        }
                    });
                }
            } else {
                $target = getLastGroupRow($cell, $tr);
                if (isGroupedRow && $target.hasClass('kv-grid-group-row')) {
                    $target.before($row);
                } else {
                    $target.after($row);
                }
            }
            /** @namespace data.mergeColumns */
            if (data.mergeColumns && data.mergeColumns.length) {
                $.each(data.mergeColumns, function (i, cols) {
                    var from = cols[0], to = cols[1], cspan = 0, merged = '';
                    if (!(from > -1 && to > -1)) {
                        return;
                    }
                    $row.find('td').each(function () {
                        var $td = $(this);
                        j = $td.attr('data-summary-col-seq');
                        if (j >= from && j <= to) {
                            merged += $td.html();
                            cspan++;
                        }
                    });
                    $row.find('td').each(function () {
                        var $td = $(this);
                        j = $td.attr('data-summary-col-seq');
                        if (j > from && j <= to) {
                            $td.remove();
                        } else {
                            if (j == from) { // jshint ignore:line
                                $td.attr('colspan', cspan).html(merged);
                            }
                        }
                    });
                });
            }
        };
        initPageSummary();
        $grid.find('td.kv-grid-group').each(function () {
            var $cell = $(this), key = $(this).attr('data-group-key');
            if (!key) {
                return;
            }
            if ($.inArray(key, groups) < 0) {
                groups.push(key);
            }
            if (data[key] === undefined) {
                data[key] = [$cell];
            } else {
                data[key].push($cell);
            }
        });
        $.each(groups, function (i, g) {
            var gCells = data[g], rowspan = 1, gCol = 0, $gCell, $prevGroup, txtCurr = '', cellKey = '',
                cellKeyPrev = '', cellKeyCurr = '';
            $.each(gCells, function (j, $cell) {
                txtCurr = $cell.text().trim();
                $gCell = gCells[gCol];
                if (i > 0) {
                    $prevGroup = getParentGroup($cell);
                    if ($prevGroup && $prevGroup.length) {
                        cellKey = $prevGroup.attr('data-cell-key');
                    }
                    cellKeyCurr = cellKey ? cellKey + '-' + txtCurr : txtCurr;
                } else {
                    cellKeyCurr = txtCurr;
                }
                $cell.attr('data-cell-key', cellKeyCurr);
                if (cellKeyCurr == cellKeyPrev) { // jshint ignore:line
                    rowspan++;
                    $gCell.attr('rowspan', rowspan);
                    $cell.addClass('kv-temp-cells').hide();
                } else {
                    gCol = j;
                    rowspan = 1;
                }
                cellKeyPrev = cellKeyCurr;
            });
        });
        $grid.find('td.kv-grid-group.kv-temp-cells').remove();
        $.each(groups, function (i, g) {
            var seq = 0;
            $grid.find('td[data-group-key="' + g + '"]').each(function () {
                var $cell = $(this), $tr, css = seq % 2 > 0 ? $cell.attr('data-odd-css') : $cell.attr('data-even-css');
                if (css) {
                    $cell.removeClass(css).addClass(css);
                }
                if ($cell.is('[data-grouped-row]')) {
                    $tr = $(document.createElement('tr')).addClass('kv-grid-group-row');
                    $cell.closest('tr').before($tr);
                    $cell.removeAttr('rowspan').appendTo($tr).css('width', 'auto');
                }
                seq++;
            });
        });
        $groupRows = $grid.find('tr.kv-grid-group-row');
        if ($groupRows.length) {
            colCount = $grid.find('tr[data-key]:first > td').length;
            if (colCount) {
                $groupRows.each(function () {
                    $(this).find('>td').attr('colspan', colCount);
                });
            }
            $groupRows.find('td[data-group-key]').each(function () {
                var gkey = $(this).data('groupKey'),
                    $head = $grid.find('.kv-grid-group-header[data-group-key]'),
                    $filt = $grid.find('.kv-grid-group-filter[data-group-key]');
                $(this).closest('tr').data('groupKey', gkey);
                if ($head.length) {
                    $grid.find('.kv-grid-group-header[data-group-key="' + gkey + '"]').remove();
                }
                if ($filt.length) {
                    $grid.find('.kv-grid-group-filter[data-group-key="' + gkey + '"]').remove();
                }
            });
        }
        $lastRow.attr('data-last-row', 1);
        n = groups.length - 1;
        for (i = n; i >= 0; i--) {
            $grid.find('td[data-group-key="' + groups[i] + '"]').each(function () {
                createSummary($(this), 'groupFooter');
            }); // jshint ignore:line
        }
        for (i = 0; i <= n; i++) {
            $grid.find('td[data-group-key="' + groups[i] + '"]').each(function () {
                createSummary($(this), 'groupHeader');
            }); // jshint ignore:line
        }
        adjustLastRow();
    };
})(window.jQuery);