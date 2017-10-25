/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2017
 * @version   3.1.7
 *
 * Grid Export Validation Module for Yii's Gridview. Supports export of
 * grid data as CSV, HTML, or Excel.
 *
 * Author: Kartik Visweswaran
 * Copyright: 2015, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
(function ($) {
    "use strict";
    var replaceAll, isEmpty, popupDialog, slug, templates, GridExport, urn = "urn:schemas-microsoft-com:office:";
    replaceAll = function (str, from, to) {
        return str.split(from).join(to);
    };
    isEmpty = function (value, trim) {
        return value === null || value === undefined || value.length === 0 || (trim && $.trim(value) === '');
    };
    popupDialog = function (url, name, w, h) {
        var left = (screen.width / 2) - (w / 2), top = 60, existWin = window.open('', name, '', true);
        existWin.close();
        return window.open(url, name,
            'toolbar=no, location=no, directories=no, status=yes, menubar=no, scrollbars=no, ' +
            'resizable=no, copyhistory=no, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);
    };
    slug = function (strText) {
        return strText.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
    };
    //noinspection XmlUnusedNamespaceDeclaration
    templates = {
        html: '<!DOCTYPE html>' +
        '<meta http-equiv="Content-Type" content="text/html;charset={encoding}"/>' +
        '<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1"/>' +
        '{css}' +
        '<style>' +
        '.kv-wrap{padding:20px;}' +
        '.kv-align-center{text-align:center;}' +
        '.kv-align-left{text-align:left;}' +
        '.kv-align-right{text-align:right;}' +
        '.kv-align-top{vertical-align:top!important;}' +
        '.kv-align-bottom{vertical-align:bottom!important;}' +
        '.kv-align-middle{vertical-align:middle!important;}' +
        '.kv-page-summary{border-top:4px double #ddd;font-weight: bold;}' +
        '.kv-table-footer{border-top:4px double #ddd;font-weight: bold;}' +
        '.kv-table-caption{font-size:1.5em;padding:8px;border:1px solid #ddd;border-bottom:none;}' +
        '</style>' +
        '<body class="kv-wrap">' +
        '{data}' +
        '</body>',
        pdf: '{before}\n{data}\n{after}',
        excel: '<html xmlns:o="' + urn + 'office" xmlns:x="' + urn + 'excel" xmlns="http://www.w3.org/TR/REC-html40">' +
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
        '<body style="display:table-cell;font-family:Helvetica,Arial,sans-serif;color:#888;font-weight:bold;line-height:1.4em;text-align:center;vertical-align:middle;width:100%;height:100%;padding:0 10px;">' +
        '{msg}' +
        '</body>' +
        '</html>'
    };
    GridExport = function (element, options) {
        //noinspection JSUnresolvedVariable
        var self = this, gridOpts = options.gridOpts, genOpts = options.genOpts;
        self.$element = $(element);
        //noinspection JSUnresolvedVariable
        self.gridId = gridOpts.gridId;
        self.$grid = $("#" + gridOpts.gridId);
        self.dialogLib = options.dialogLib;
        self.messages = gridOpts.messages;
        self.target = gridOpts.target;
        self.exportConversions = gridOpts.exportConversions;
        self.showConfirmAlert = gridOpts.showConfirmAlert;
        self.filename = genOpts.filename;
        self.expHash = genOpts.expHash;
        self.showHeader = genOpts.showHeader;
        self.showFooter = genOpts.showFooter;
        self.showPageSummary = genOpts.showPageSummary;
        self.$table = self.$grid.find('.kv-grid-table:first');
        self.$form = self.$grid.find('form.kv-export-form');
        self.encoding = self.$form.find('[name="export_encoding"]').val();
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
            /** @namespace self.config.colHeads */
            /** @namespace self.config.slugColHeads */
            if (self.config.colHeads !== undefined && self.config.colHeads.length > 0) {
                head = self.config.colHeads;
            } else {
                $table.find('thead tr th').each(function (i) {
                    var str = $(this).text().trim(), slugStr = slug(str);
                    head[i] = (!self.config.slugColHeads || isEmpty(slugStr)) ? 'col_' + i : slugStr;
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
                }, 4000);
            } else {
                var newmsg = templates.popup.replace('{msg}', msg);
                self.popup.document.write(newmsg);
            }
        },
        processExport: function (callback, arg) {
            var self = this;
            setTimeout(function () {
                if (!isEmpty(arg)) {
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
                var msgs = self.messages, msg1 = isEmpty(self.alertMsg) ? '' : self.alertMsg,
                    msg2 = isEmpty(msgs.allowPopups) ? '' : msgs.allowPopups,
                    msg3 = isEmpty(msgs.confirmDownload) ? '' : msgs.confirmDownload, msg = '';
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
                if (isEmpty(msg)) {
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
            if (self.target === '_popup') {
                self.$form.on('submit.gridexport', function () {
                    setTimeout(function () {
                        self.setPopupAlert(self.messages.downloadComplete, true);
                    }, 1000);
                });
            }
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
            var self = this, $table = self.$table.clone(), $tHead,
                $container = self.$table.closest('.kv-grid-container'),
                safeRemove = function (selector) {
                    $table.find(selector + '.' + self.gridId).remove();
                };
    
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
            $table.find('input').remove(); // remove any form inputs
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
            var htmlContent = $table.html();
            htmlContent = self.preProcess(htmlContent);
            $table.html(htmlContent);
            return $table;
        },
        preProcess: function (content) {
            var self = this, conv = self.exportConversions, l = conv.length, processed = content, c;
            if (l > 0) {
                for (var i = 0; i < l; i++) {
                    c = conv[i];
                    processed = replaceAll(processed, c.from, c.to);
                }
            }
            return processed;
        },
        download: function (type, content) {
            var self = this, $el = self.$element, mime = $el.attr('data-mime') || 'text/plain',
                hashData = $el.attr('data-hash') || '', config = isEmpty(self.config) ? {} : self.config,
                setValue = function (f, v) {
                    self.$form.find('[name="export_' + f + '"]').val(v);
                };
            if (type === 'json' && config.jsonReplacer) {
                delete config.jsonReplacer;
            }
            setValue('filetype', type);
            setValue('filename', self.filename);
            setValue('content', content);
            setValue('mime', mime);
            setValue('hash', hashData);
            setValue('config', JSON.stringify(config));
            if (self.target === '_popup') {
                self.popup = popupDialog('', 'kvDownloadDialog', 350, 120);
                self.popup.focus();
                self.setPopupAlert(self.messages.downloadProgress);
            }
            self.$form.submit();
        },
        exportHTML: function () {
            /** @namespace self.config.cssFile */
            var self = this, $table = self.clean('html'), cfg = self.config,
                css = (self.config.cssFile && cfg.cssFile.length) ? '<link href="' + self.config.cssFile + '" rel="stylesheet">' : '',
                html = templates.html.replace('{encoding}', self.encoding).replace('{css}', css).replace('{data}',
                    $('<div />').html($table).html());
            self.download('html', html);
        },
        exportPDF: function () {
            var self = this, $table = self.clean('pdf');
            /** @namespace self.config.contentAfter */
            /** @namespace self.config.contentBefore */
            var before = isEmpty(self.config.contentBefore) ? '' : self.config.contentBefore,
                after = isEmpty(self.config.contentAfter) ? '' : self.config.contentAfter,
                css = self.config.css,
                pdf = templates.pdf.replace('{css}', css)
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
            /** @namespace self.config.rowDelimiter */
            /** @namespace self.config.colDelimiter */
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
            /** @namespace self.config.indentSpace */
            /** @namespace self.config.jsonReplacer */
            out = JSON.stringify(out, self.config.jsonReplacer, self.config.indentSpace);
            self.download('json', out);
        },
        exportEXCEL: function () {
            var self = this, $table = self.clean('xls'), cfg = self.config, xls, $td,
                css = (cfg.cssFile && self.config.cssFile.length) ? '<link href="' + self.config.cssFile + '" rel="stylesheet">' : '';
            $table.find('td[data-raw-value]').each(function () {
                $td = $(this);
                if ($td.css('mso-number-format') || $td.css('mso-number-format') === 0 || $td.css(
                        'mso-number-format') === '0') {
                    $td.html($td.attr('data-raw-value')).removeAttr('data-raw-value');
                }
            });
            /** @namespace self.config.worksheet */
            xls = templates.excel.replace('{encoding}', self.encoding).replace('{css}', css).replace('{worksheet}',
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