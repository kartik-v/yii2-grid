/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   3.2.7
 *
 * Grid grouping jquery library created for yii2-grid.
 *
 * Author: Kartik Visweswaran
 * Copyright: 2014 - 2019, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
var kvGridGroup;
(function ($) {
    'use strict';
    kvGridGroup = function (gridId) {
        var $grid, data, groups, $groupRows, i, n, colCount, $pageSum, $firstRow, $lastRow, isEmpty, formatNumber,
            calculate, getParentGroup, getLastGroupRow, getCellKey, getCellValue, getSummarySource, getSummaryContent,
            initPageSummary, addRowSpan, adjustLastRow, adjustFooterGroups, createSummary, calculateSummaryContent,
            ROW = 'tr.' + gridId, COL = 'td.' + gridId;
        $grid = $('#' + gridId);
        data = {};
        groups = [];
        colCount = 0;
        $pageSum = $grid.find(ROW + '.kv-page-summary');
        $firstRow = $grid.find(ROW + '[data-key]:first');
        $lastRow = $grid.find(ROW + '[data-key]:last');
        isEmpty = function (v) {
            return v === undefined || v === null || v.length === 0;
        };
        /**
         * Format a number
         * @param n float, the number
         * @param d integer, length of decimal (defaults to 2)
         * @param c mixed, decimal delimiter (defaults to ".")
         * @param s mixed, sections delimiter (defaults to ",")
         * @param x integer, length of whole part (defaults to 3 for thousands)
         * @returns string
         */
        formatNumber = function (n, d, c, s, x) {
            var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (d > 0 ? '\\D' : '$') + ')',
                num = parseFloat(n), dec = parseInt(d), newNum;
            if (isNaN(num)) {
                return '';
            }
            newNum = num + '';
            c = c || '.';
            s = s || ',';
            if (newNum.indexOf('.') === -1 && dec > 0) {
                num = parseFloat(num + '.0');
            }
            newNum = num.toFixed(isNaN(dec) || dec < 0 ? 0 : dec);
            newNum = newNum.replace('.', c);
            return newNum.replace(new RegExp(re, 'g'), '$&' + s);
        };
        calculate = function (data, func) {
            var i, fn, out = 0, n = data && data.length || 0;
            if (!n) {
                return '';
            }
            switch (func) {
                case 'f_count':
                    return n;
                case 'f_sum':
                case 'f_avg':
                    for (i = 0; i < n; i++) {
                        out += data[i];
                    }
                    return func === 'f_sum' ? out : out / n;
                case 'f_max':
                case 'f_min':
                    fn = func === 'f_max' ? 'max' : 'min';
                    return Math[fn].apply(null, data);
                default:
                    return '';
            }
        };
        getParentGroup = function ($cell) {
            var $tr, $td, id = $cell.attr('data-sub-group-of'), i, tag;
            if (isEmpty(id)) {
                return null;
            }
            tag = COL + '[data-col-seq="' + id + '"]';
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
                i = $tr.find(COL + '[data-group-key="' + key + '"]').length;
            }
            return $endRow.length ? $endRow : $lastRow;
        };
        getCellKey = function ($cell) {
            var $currCell = $cell, key = '';
            while ($currCell && $currCell.length) {
                key += $currCell.text().trim();
                $currCell = getParentGroup($currCell);
            }
            return key;
        };
        getCellValue = function ($cell, decPoint, thousandSep) {
            var out;
            if (!$cell || !$cell.length) {
                return 0;
            }
            if ($cell.is('[data-raw-value]')) {
                out = $cell.attr('data-raw-value');
            } else {
                out = $cell.text().split(thousandSep || ',').join('');
                if (decPoint && decPoint !== '.') {
                    out = out.replace(decPoint, '.');
                }
            }
            return out ? parseFloat(out) : 0;
        };
        getSummarySource = function ($tr, $td, i, decPoint, thousandSep) {
            var j = 1, data = [], $row = $tr, isGrouped = $row.hasClass('kv-grid-group-row'),
                rowspan = $td.attr('rowspan') || 1;
            if (isGrouped) {
                j = false;
                $row = $row.next(':not(.kv-grid-group-row)');
                while (!j && $row.length) {
                    $row.find(' > td[data-col-seq="' + i + '"]').each(function () {
                        data.push(getCellValue($(this), decPoint, thousandSep));
                    }); // jshint ignore:line
                    j = $row.hasClass('kv-grid-group-row');
                    $row = $row.next();
                }
            } else {
                while (j <= rowspan && $row.length) {
                    $row.find(' > td[data-col-seq="' + i + '"]').each(function () {
                        data.push(getCellValue($(this), decPoint, thousandSep));
                    }); // jshint ignore:line
                    $row = $row.next();
                    j++;
                }
            }
            return data;
        };
        getSummaryContent = function (source, $tr, $td, i, config) {
            // noinspection JSUnresolvedVariable
            var fmt = config.format || '', func = config.func ? window[config.func] : '',
                decPoint = config.decPoint || '.', thousandSep = config.thousandSep || ',',
                data = (fmt === 'number' || fmt === 'callback' && typeof func === 'function') ?
                    getSummarySource($tr, $td, i, decPoint, thousandSep) :
                    source;
            return calculateSummaryContent(source, data, config);
        };
        initPageSummary = function () {
            var i = 0;
            if (!$pageSum.length) {
                return;
            }
            $pageSum.find(' > td').each(function () {
                $(this).attr('data-col-seq', i);
                i++;
            });
        };
        calculateSummaryContent = function (source, data, config) {
            // noinspection JSUnresolvedVariable
            var decimals = config.decimals || 0, decPoint = config.decPoint || '.',
                thousandSep = config.thousandSep || ',', fmt = config.format || '',
                func = config.func ? window[config.func] : '', out;
            if (fmt === 'number') {
                out = calculate(data, source);
                return formatNumber(out, decimals, decPoint, thousandSep);
            }
            if (fmt === 'callback' && typeof func === 'function') {
                return func(data);
            }
            return source;
        };
        addRowSpan = function ($el, n) {
            if ($el[0].hasAttribute('data-grouped-row')) {
                return;
            }
            n = n || 1;
            var rowspan = $el.attr('rowspan') || 1;
            rowspan = parseInt(rowspan) + n;
            $el.attr('rowspan', rowspan);
        };
        adjustLastRow = function () {
            var i, rows = [];
            $lastRow.nextAll(ROW + '.kv-group-footer').each(function () {
                rows.push($(this));
            });
            if (rows.length) {
                for (i = 0; i < rows.length; i++) {
                    $lastRow.after(rows[i]);
                }
            }
            if ($pageSum.length) {
                $pageSum.find(' > td').each(function () {
                    var $td = $(this);
                    if (!$firstRow.find(' > td[data-col-seq="' + $td.attr('data-col-seq') + '"]').length) {
                        $td.remove();
                    }
                });
            }
        };
        adjustFooterGroups = function () {
            var len = groups.length, $tbody = $grid.find('tbody:first'), j,
                hasFooter = $tbody.find(ROW + '.kv-group-footer').length;
            if (len < 3 || !hasFooter) {
                return;
            }
            $tbody.find(' > ' + ROW + '[data-group-key]').each(function () {
                var $row = $(this);
                $row.find('> td.kv-grid-group').each(function () {
                    var $td = $(this), grpSeq = $td.attr('data-sub-group-of') || '0', rowspan = 0, proceed = true;
                    $row.nextAll().each(function () {
                        if (!proceed) {
                            return;
                        }
                        rowspan++;
                        if ($(this).attr('data-group-seq') === grpSeq) {
                            proceed = false;
                        }
                    });
                    if (!$td[0].hasAttribute('data-grouped-row')) {
                        $td.attr('rowspan', rowspan);
                    }
                });
            });
            $tbody.find(' > ' + ROW + '.kv-group-footer').each(function () {
                var $tr = $(this), i = parseInt($tr.attr('data-group-seq') || 0);
                for (j = 1; j <= i; j++) {
                    $tr.find('> td[data-summary-col-seq=' + j + ']').remove();
                }
            });
            // summary correction for multi group footers
            $tbody.find(' > ' + ROW + '.kv-group-footer > td').each(function () {
                var $td = $(this), sumData = $td.data('groupSummary') || null, config, data = [], seq, grpSeq,
                    $tr, proceed = true, out;
                if (!sumData) {
                    return;
                }
                config = sumData.config;
                // noinspection JSUnresolvedVariable
                if (!config.format && !config.func) {
                    return;
                }
                seq = $td.attr('data-summary-col-seq');
                $tr = $td.closest('tr');
                grpSeq = $tr.attr('data-group-seq');
                $tr.prevAll('.' + gridId).each(function () {
                    var $row = $(this), i = $row.attr('data-group-seq') || '-1', content;
                    if (!proceed) {
                        return;
                    }
                    // noinspection JSUnresolvedVariable
                    content = getCellValue($row.find('> td[data-col-seq=' + seq + ']'), config.decPoint,
                        config.thousandSep);
                    data.push(content);
                    if (i === grpSeq) {
                        proceed = false;
                    }
                });
                out = calculateSummaryContent(sumData.source || '', data, config);
                $td.html(out);
            });
        };
        createSummary = function ($cell, type) {
            var data = $cell.data(type), $parent, key, $tr, $td, i, j, $row, $col, $target, content, config,
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
            $row = $(document.createElement('tr')).addClass(gridId);
            if (data.options) {
                $row.attr(data.options);
            }
            $row.addClass(css).addClass(gridId).attr({'data-group-seq': key || '0'});
            $firstRow.find('> td').each(function () {
                var summary;
                $td = $(this);
                i = $td.attr('data-col-seq');
                if (!key || i !== key || isGroupedRow) { // jshint ignore:line
                    $col = $(document.createElement('td')).addClass(gridId).attr('data-summary-col-seq', i);
                    if (data.content && data.content[i]) {
                        // noinspection JSUnresolvedVariable
                        config = data.contentFormats && data.contentFormats[i] || {};
                        content = getSummaryContent(data.content[i], $tr, $cell, i, config);
                        summary = {source: data.content[i], config: config};
                        $col.html(content).data('groupSummary', summary);
                    }
                    // noinspection JSUnresolvedVariable
                    if (data.contentOptions && data.contentOptions[i]) {
                        $col.attr(data.contentOptions[i]);
                    }
                    if ($td.hasClass('kv-grid-hide')) {
                        $col.addClass('kv-grid-hide');
                    }
                    if ($td.hasClass('skip-export')) {
                        $col.addClass('skip-export');
                    }
                    $col.appendTo($row);
                }
            });
            if ($parent && $parent.length && !isGroupedRow && groups.length < 3) {
                addRowSpan($parent);
            }
            if (type === 'groupHeader') {
                $tr.before($row);
                if ($tr.find('> td[data-col-seq="' + key + '"]').length) {
                    $row.find(' > td').each(function () {
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
            // noinspection JSUnresolvedVariable
            if (data.mergeColumns && data.mergeColumns.length) {
                $.each(data.mergeColumns, function (i, cols) {
                    var from = cols[0], to = cols[1], cspan = 0, merged = '';
                    if (!(from > -1 && to > -1)) {
                        return;
                    }
                    $row.find(' > td').each(function () {
                        var $td = $(this);
                        j = $td.attr('data-summary-col-seq');
                        if (j >= from && j <= to) {
                            merged += $td.html();
                            cspan++;
                        }
                    });
                    $row.find(' > td').each(function () {
                        var $td = $(this);
                        j = parseInt($td.attr('data-summary-col-seq') || -1);
                        if (j > from && j <= to) {
                            $td.remove();
                        } else {
                            if (j === from) { // jshint ignore:line
                                $td.attr('colspan', cspan).html(merged);
                            }
                        }
                    });
                });
            }
        };
        initPageSummary();
        $grid.find(ROW + '> td.kv-grid-group').each(function () {
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
            var gCells = data[g], rowspan = 1, gCol = 0, $gCell, cellKeyPrev = '', cellKeyCurr = '';
            $.each(gCells, function (j, $cell) {
                $gCell = gCells[gCol];
                cellKeyCurr = i > 0 ? getCellKey($cell) : $cell.text().trim();
                if (cellKeyCurr === cellKeyPrev) {
                    rowspan++;
                    if (!$gCell[0].hasAttribute('data-grouped-row')) {
                        $gCell.attr('rowspan', rowspan);
                    }
                    $cell.addClass('kv-temp-cells').hide();
                } else {
                    gCol = j;
                    rowspan = 1;
                }
                cellKeyPrev = cellKeyCurr;
            });
        });
        $grid.find(ROW + '> td.kv-grid-group.kv-temp-cells').remove();
        $.each(groups, function (i, g) {
            var seq = 0;
            $grid.find(ROW + '> td[data-group-key="' + g + '"]').each(function () {
                var $cell = $(this), $tr, css = seq % 2 > 0 ? $cell.attr('data-odd-css') : $cell.attr('data-even-css');
                if (css) {
                    $cell.removeClass(css).addClass(css);
                }
                if ($cell.is('[data-grouped-row]')) {
                    $tr = $(document.createElement('tr')).addClass('kv-grid-group-row ' + gridId);
                    $cell.closest('tr').before($tr);
                    $cell.removeAttr('rowspan').appendTo($tr).css('width', 'auto');
                }
                seq++;
            });
        });
        $groupRows = $grid.find(ROW + '.kv-grid-group-row');
        if ($groupRows.length) {
            colCount = $grid.find(ROW + '[data-key]:first > td').length;
            if (colCount) {
                $groupRows.each(function () {
                    $(this).find('>td').attr('colspan', colCount);
                });
            }
            $groupRows.find('> td[data-group-key]').each(function () {
                var HDR = '.' + gridId + '.kv-grid-group-header', FIL = '.' + gridId + '.kv-grid-group-filter',
                    gkey = $(this).data('groupKey'),
                    $head = $grid.find(HDR + '[data-group-key]'),
                    $filt = $grid.find(FIL + '[data-group-key]');
                $(this).closest('tr').data('groupKey', gkey);
                if ($head.length) {
                    $grid.find(HDR + '[data-group-key="' + gkey + '"]').remove();
                }
                if ($filt.length) {
                    $grid.find(FIL + '[data-group-key="' + gkey + '"]').remove();
                }
            });
        }
        $lastRow.attr('data-last-row', 1);
        n = groups.length - 1;
        for (i = n; i >= 0; i--) {
            $grid.find(ROW + '> td[data-group-key="' + groups[i] + '"]').each(function () {
                createSummary($(this), 'groupFooter');
            }); // jshint ignore:line
        }
        for (i = 0; i <= n; i++) {
            $grid.find(ROW + '> td[data-group-key="' + groups[i] + '"]').each(function () {
                createSummary($(this), 'groupHeader');
            }); // jshint ignore:line
        }
        adjustLastRow();
        adjustFooterGroups();
    };
})(window.jQuery);