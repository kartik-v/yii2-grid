/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   3.3.4
 *
 * Grid Export Validation Module for Yii's Gridview. Supports export of
 * grid data as CSV, HTML, or Excel.
 *
 * Author: Kartik Visweswaran
 * Copyright: 2014 - 2019, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
(function ($) {
    "use strict";
    var $h, GridExport, URN = 'urn:schemas-microsoft-com:office:', XMLNS = 'http://www.w3.org/TR/REC-html40';
    // noinspection XmlUnusedNamespaceDeclaration
    $h = {
        replaceAll: function (str, from, to) {
            return str.split(from).join(to);
        },
        isEmpty: function (value, trim) {
            return value === null || value === undefined || value.length === 0 || (trim && $.trim(value) === '');
        },
        popupDialog: function (url, name, w, h) {
            var left = (screen.width / 2) - (w / 2), top = 60, existWin = window.open('', name, '', true);
            existWin.close();
            return window.open(url, name,
                'toolbar=no, location=no, directories=no, status=yes, menubar=no, scrollbars=no, ' +
                'resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
        },
        slug: function (strText) {
            return strText.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
        },
        templates: {
            html: '<!DOCTYPE html>' +
            '<meta http-equiv="Content-Type" content="text/html;charset={encoding}"/>' +
            '<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1"/>' +
            '{css}' +
            '<style>.kv-wrap{padding:20px}</style>' +
            '<body class="kv-wrap">' +
            '{data}' +
            '</body>',
            pdf: '{before}\n{data}\n{after}',
            excel: '<html xmlns:o="' + URN + 'office" xmlns:x="' + URN + 'excel" xmlns="' + XMLNS + '">' +
            '<head>' +
            '<meta http-equiv="Content-Type" content="text/html;charset={encoding}"/>' +
            '{css}' +
            '<!--[if gte mso 9]>' +
            '<xml>' +
            '<x:ExcelWorkbook>' +
            '<x:ExcelWorksheets>' +
            '<x:ExcelWorksheet>' +
            '<x:Name>{worksheet}</x:Name>' +
            '<x:WorksheetOptions>' +
            '<x:DisplayGridlines/>' +
            '</x:WorksheetOptions>' +
            '</x:ExcelWorksheet>' +
            '</x:ExcelWorksheets>' +
            '</x:ExcelWorkbook>' +
            '</xml>' +
            '<![endif]-->' +
            '</head>' +
            '<body>' +
            '{data}' +
            '</body>' +
            '</html>',
            popup: '<html style="display:table;width:100%;height:100%;">' +
            '<title>Grid Export - &copy; Krajee</title>' +
            '<body style="display:table-cell;font-family:Helvetica,Arial,sans-serif;color:#888;font-weight:bold' +
            ';line-height:1.4em;text-align:center;vertical-align:middle;width:100%;height:100%;padding:0 10px;">' +
            '{msg}' +
            '</body>' +
            '</html>'
        }
    };
    GridExport = function (element, options) {
        //noinspection JSUnresolvedVariable
        var self = this, gridOpts = options.gridOpts, genOpts = options.genOpts;
        self.$element = $(element);
        //noinspection JSUnresolvedVariable
        self.gridId = gridOpts.gridId;
        self.$grid = $("#" + self.gridId);
        self.dialogLib = options.dialogLib;
        self.messages = gridOpts.messages;
        self.target = gridOpts.target;
        self.exportConversions = gridOpts.exportConversions;
        self.skipExportElements = gridOpts.skipExportElements;
        self.showConfirmAlert = gridOpts.showConfirmAlert;
        self.action = gridOpts.action;
        self.bom = gridOpts.bom;
        self.encoding = gridOpts.encoding;
        self.module = gridOpts.module;
        self.filename = genOpts.filename;
        self.expHash = genOpts.expHash;
        self.showHeader = genOpts.showHeader;
        self.showFooter = genOpts.showFooter;
        self.showPageSummary = genOpts.showPageSummary;
        self.$table = self.$grid.find('.kv-grid-table:first');
        self.columns = self.showHeader ? 'td,th' : 'td';
        self.alertMsg = options.alertMsg;
        self.config = options.config;
        self.popup = '';
        self.listen();
    };

    GridExport.prototype = {
        constructor: GridExport,
        getArray: function (expType) {
            var self = this, $table = self.clean(expType), head = [], data = {};
            if (self.config.colHeads !== undefined && self.config.colHeads.length > 0) {
                head = self.config.colHeads;
            } else {
                $table.find('thead tr th').each(function (i) {
                    var str = $(this).text().trim(), slugStr = $h.slug(str);
                    head[i] = (!self.config.$h.slugColHeads || $h.isEmpty(slugStr)) ? 'col_' + i : slugStr;
                });
            }
            $table.find('tbody tr:has("td")').each(function (i) {
                data[i] = {};
                //noinspection JSValidateTypes
                $(this).children('td').each(function (j) {
                    var col = head[j];
                    data[i][col] = $(this).text().trim();
                });
            });
            return data;
        },
        setPopupAlert: function (msg) {
            var self = this;
            if (self.popup.document === undefined) {
                return;
            }
            if (arguments.length && arguments[1]) {
                var el = self.popup.document.getElementsByTagName('body');
                setTimeout(function () {
                    el[0].innerHTML = msg;
                }, 1200);
            } else {
                var newmsg = $h.templates.popup.replace('{msg}', msg);
                self.popup.document.write(newmsg);
            }
        },
        processExport: function (callback, arg) {
            var self = this;
            setTimeout(function () {
                if (!$h.isEmpty(arg)) {
                    self[callback](arg);
                } else {
                    self[callback]();
                }
            }, 100);
        },
        listenClick: function (callback) {
            var self = this, arg = arguments.length > 1 ? arguments[1] : '', lib = window[self.dialogLib];
            self.$element.off("click.gridexport").on("click.gridexport", function (e) {
                e.stopPropagation();
                e.preventDefault();
                if (!self.showConfirmAlert) {
                    self.processExport(callback, arg);
                    return;
                }
                var msgs = self.messages, msg1 = $h.isEmpty(self.alertMsg) ? '' : self.alertMsg,
                    msg2 = $h.isEmpty(msgs.allowPopups) ? '' : msgs.allowPopups,
                    msg3 = $h.isEmpty(msgs.confirmDownload) ? '' : msgs.confirmDownload, msg = '';
                if (msg1.length && msg2.length) {
                    msg = msg1 + '\n\n' + msg2;
                } else {
                    if (!msg1.length && msg2.length) {
                        msg = msg2;
                    } else {
                        msg = (msg1.length && !msg2.length) ? msg1 : '';
                    }
                }
                if (msg3.length) {
                    msg = msg + '\n\n' + msg3;
                }
                if ($h.isEmpty(msg)) {
                    return;
                }
                lib.confirm(msg, function (result) {
                    if (result) {
                        self.processExport(callback, arg);
                    }
                    e.preventDefault();
                });
                return false;
            });
        },
        listen: function () {
            var self = this;
            if (self.$element.hasClass('export-csv')) {
                self.listenClick('exportTEXT', 'csv');
            }
            if (self.$element.hasClass('export-txt')) {
                self.listenClick('exportTEXT', 'txt');
            }
            if (self.$element.hasClass('export-html')) {
                self.listenClick('exportHTML');
            }
            if (self.$element.hasClass('export-xls')) {
                self.listenClick('exportEXCEL');
            }
            if (self.$element.hasClass('export-json')) {
                self.listenClick('exportJSON');
            }
            if (self.$element.hasClass('export-pdf')) {
                self.listenClick('exportPDF');
            }
        },
        clean: function (expType) {
            var self = this, $table = self.$table.clone(), $tHead, cssStyles = self.$element.data('cssStyles') || {},
                $container = self.$table.closest('.kv-grid-container'), skipElements = self.skipExportElements,
                safeRemove = function (selector) {
                    $table.find(selector + '.' + self.gridId).remove();
                };
            if (skipElements.length) {
                $.each(skipElements, function(key, selector) {
                    $table.find(selector).remove();
                });
            }
            if (expType === 'html') {
                $table.find('.kv-grid-boolean').remove();
            }
            if ($container.hasClass('kv-grid-wrapper')) {
                $tHead = $container.closest('.floatThead-wrapper').find('.kv-thead-float thead');
            } else {
                $tHead = $container.find('.kv-thead-float thead');
            }
            if ($tHead.length) {
                $tHead = $tHead.clone();
                $table.find('thead').before($tHead).remove();
            }
            // Skip the filter rows and header rowspans
            $table.find('tr.filters').remove();
            $table.find('th').removeAttr('rowspan');
            // remove link tags
            $table.find('th').find('a').each(function () {
                $(this).contents().unwrap();
            });
            $table.find('form,input,textarea,select,script').remove(); // remove form, inputs, scripts
            $table.find('[onclick]').removeAttr('onclick');
            $table.find('a[href*="javascript"]').attr('href', '#');
            if (!self.showHeader) {
                $table.children('thead').remove();
            }
            if (!self.showPageSummary) {
                safeRemove('.kv-page-summary-container');
            }
            if (!self.showFooter) {
                safeRemove('.kv-footer-container');
            }
            if (!self.showCaption) {
                safeRemove('.kv-caption-container');
            }
            $table.find('.skip-export').remove();
            $table.find('.skip-export-' + expType).remove();
            $table.find('.strip-tags-export').each(function () {
                var $el = $(this), rawText = $el.text();
                $el.html(rawText);
            });
            var htmlContent = $table.html();
            htmlContent = self.preProcess(htmlContent, expType);
            $table.html(htmlContent);
            $.each(cssStyles, function (key, value) {
                $table.find(key).each(function() {
                    var $el = $(this), styles = $el.attr('style') || '';
                    $.each(value, function (fm, to) {
                        styles += fm + ':' + to + ';'
                    });
                    if (styles) {
                        $el.attr('style', styles);
                    }
                });
            });
            return $table;
        },
        preProcess: function (content, expType) {
            var self = this, conv = self.exportConversions, l = conv.length, processed = content, c, from, to,
                fmParam = 'from_' + expType, toParam = 'to_' + expType;
            if (l > 0) {
                for (var i = 0; i < l; i++) {
                    c = conv[i];
                    from = c[fmParam] !== undefined ? c[fmParam] : (c.from !== undefined ? c.from : '');
                    to = c[toParam] !== undefined ? c[toParam] : (c.to !== undefined ? c.to : '');
                    if (from.length && to.length) {
                        processed = $h.replaceAll(processed, from, to);
                    }
                }
            }
            return processed;
        },
        download: function (type, content) {
            var self = this, $el = self.$element, mime = $el.attr('data-mime') || 'text/plain', yiiLib = window.yii,
                hashData = $el.attr('data-hash') || '', hashConfig = $el.attr('data-hash-export-config'), config = $h.isEmpty(self.config) ? {} : self.config,
                $csrf, isPopup, target = self.target, getInput = function (name, value) {
                    return $('<textarea/>', {'name': name}).val(value).hide();
                };
            if (type === 'json' && config.jsonReplacer) {
                delete config.jsonReplacer;
            }
            $csrf = yiiLib ? getInput(yiiLib.getCsrfParam() || '_csrf', yiiLib.getCsrfToken() || null) : null;
            isPopup = target === '_popup';
            if (isPopup) {
                target = 'kvDownloadDialog';
                self.popup = $h.popupDialog('', target, 350, 120);
                self.popup.focus();
                self.setPopupAlert(self.messages.downloadProgress);
            }
            $('<form/>', {'action': self.action, 'target': target, 'method': 'post', css: {'display': 'none'}})
                .append(getInput('export_filetype', type), getInput('export_filename', self.filename))
                .append(getInput('export_encoding', self.encoding), getInput('export_bom', self.bom ? 1 : 0))
                .append(getInput('export_content', content), getInput('module_id', self.module), $csrf)
                .append(getInput('export_mime', mime), getInput('export_hash', hashData), getInput('hash_export_config', hashConfig))
                .append(getInput('export_config', JSON.stringify(config)))
                .appendTo('body')
                .submit()
                .remove();
            if (isPopup) {
                self.setPopupAlert(self.messages.downloadComplete, true);
            }
        },
        exportHTML: function () {
            var self = this, $table = self.clean('html'), cfg = self.config, css = cfg.cssFile ? cfg.cssFile : [], html,
                cssString = '';
            html = $h.templates.html.replace('{encoding}', self.encoding);
            $.each(css, function (key, value) {
                cssString += '<link href="' + value + '" rel="stylesheet" crossorigin="anonymous">\n';
            });
            html = html.replace('{css}', cssString).replace('{data}', $('<div />').html($table).html());
            self.download('html', html);
        },
        exportPDF: function () {
            var self = this, $table = self.clean('pdf');
            var before = $h.isEmpty(self.config.contentBefore) ? '' : self.config.contentBefore,
                after = $h.isEmpty(self.config.contentAfter) ? '' : self.config.contentAfter,
                css = self.config.css,
                pdf = $h.templates.pdf.replace('{css}', css)
                    .replace('{before}', before)
                    .replace('{after}', after)
                    .replace('{data}', $('<div />').html($table).html());
            self.download('pdf', pdf);
        },
        exportTEXT: function (expType) {
            var self = this, $table = self.clean(expType),
                $rows = $table.find('tr:has(' + self.columns + ')');
            // temporary delimiter characters unlikely to be typed by keyboard,
            // this is to avoid accidentally splitting the actual contents
            var tmpColDelim = String.fromCharCode(11), // vertical tab character
                tmpRowDelim = String.fromCharCode(0); // null character
            // actual delimiter characters for CSV format
            var colD = '"' + self.config.colDelimiter + '"', rowD = '"' + self.config.rowDelimiter + '"';
            // grab text from table into CSV formatted string
            var txt = '"' + $rows.map(function (i, row) {
                var $row = $(row), $cols = $row.find(self.columns);
                return $cols.map(function (j, col) {
                    var $col = $(col), text = $col.text().trim();
                    return text.replace(/"/g, '""'); // escape double quotes
                }).get().join(tmpColDelim);
            }).get().join(tmpRowDelim)
                .split(tmpRowDelim).join(rowD)
                .split(tmpColDelim).join(colD) + '"';
            self.download(expType, txt);
        },
        exportJSON: function () {
            var self = this, out = self.getArray('json');
            out = JSON.stringify(out, self.config.jsonReplacer, self.config.indentSpace);
            self.download('json', out);
        },
        exportEXCEL: function () {
            var self = this, $table = self.clean('xls'), cfg = self.config, xls, $td,
                css = (cfg.cssFile && self.config.cssFile.length) ? '<link href="' + self.config.cssFile + '" rel="stylesheet">' : '';
            $table.find('td[data-raw-value]').each(function () {
                $td = $(this);
                if ($td.css('mso-number-format') || $td.css('mso-number-format') === 0 ||
                    $td.css('mso-number-format') === '0') {
                    $td.html($td.attr('data-raw-value')).removeAttr('data-raw-value');
                }
            });
            xls = $h.templates.excel.replace('{encoding}', self.encoding).replace('{css}', css).replace('{worksheet}',
                self.config.worksheet).replace('{data}', $('<div />').html($table).html()).replace(/"/g, '\'');
            self.download('xls', xls);
        }
    };

    //GridExport plugin definition
    $.fn.gridexport = function (option) {
        var args = Array.apply(null, arguments);
        args.shift();
        return this.each(function () {
            var $this = $(this),
                data = $this.data('gridexport'),
                options = typeof option === 'object' && option;

            if (!data) {
                $this.data('gridexport',
                    (data = new GridExport(this, $.extend({}, $.fn.gridexport.defaults, options, $(this).data()))));
            }

            if (typeof option === 'string') {
                data[option].apply(data, args);
            }
        });
    };

    $.fn.gridexport.defaults = {dialogLib: 'krajeeDialog'};
    $.fn.gridexport.Constructor = GridExport;
})(window.jQuery);