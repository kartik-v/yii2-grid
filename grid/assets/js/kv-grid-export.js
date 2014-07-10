/*!
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @version 1.6.0
 *
 * Grid Export Validation Module for Yii's Gridview. Supports export of
 * grid data as CSV, HTML, or Excel.
 *
 * Author: Kartik Visweswaran
 * Copyright: 2014, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
function replaceAll(str, from, to) {
    return str.split(from).join(to);
}
(function ($) {
    var HTML_TEMPLATE =
        '<!DOCTYPE html>' +
            '<meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>' +
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
            '</body>';

    var EXCEL_TEMPLATE =
        '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel"' +
            'xmlns="http://www.w3.org/TR/REC-html40">' +
            '<head>' +
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
            '</html>';

    var GridExport = function (element, options) {
        this.$element = $(element);
        this.$grid = options.grid;
        this.$table = this.$grid.find('table');
        this.$form = this.$grid.find('form.kv-export-form');
        this.filename = options.filename;
        this.showHeader = options.showHeader;
        this.columns = options.showHeader ? 'td,th' : 'td';
        this.worksheet = options.worksheet;
        this.colDelimiter = options.colDelimiter;
        this.rowDelimiter = options.rowDelimiter;
        this.alertMsg = options.alertMsg;
        this.browserPopupsMsg = options.browserPopupsMsg;
        this.cssFile = options.cssFile;
        this.exportConversions = options.exportConversions;
        this.listen();
    };
    
    GridExport.prototype = {
        constructor: GridExport,
        notify: function () {
            var self = this;
            var msg1 = self.alertMsg.length ? self.alertMsg : '',
                msg2 = self.browserPopupsMsg.length ? self.browserPopupsMsg : '',
                msg = '';
            if (msg1.length && msg2.length) {
                msg = msg1 + '\n\n' + msg2;
            }
            else if (!msg1.length && msg2.length) {
                msg = msg2;
            }
            else if (msg1.length && !msg2.length) {
                msg = msg1;
            }
            else {
                return;
            }
        },
        listen: function () {
            var self = this;
            self.$form.on('submit', function() {
                setTimeout(function () {
                    self.$grid.yiiGridView("applyFilter");
                }, 500);
            });
            if (self.$element.hasClass('export-csv')) {
                self.$element.on("click", function (e) {
                    self.notify();
                    self.exportTEXT('csv');
                    e.preventDefault();
                });
            }
            else if (self.$element.hasClass('export-txt')) {
                self.$element.on("click", function (e) {
                    self.notify();
                    self.exportTEXT('txt');
                    e.preventDefault();
                });
            }
            else if (self.$element.hasClass('export-html')) {
                self.$element.on("click", function (e) {
                    self.notify();
                    self.exportHTML();
                    e.preventDefault();
                });
            }
            else if (self.$element.hasClass('export-xls')) {
                self.$element.on("click", function (e) {
                    self.notify();
                    self.exportEXCEL();
                    e.preventDefault();
                });
            }
        },
        clean: function ($type) {
            var self = this, $table = self.$table.clone();
            // Skip the filter rows and header rowspans
            $table.find('tr.filters').remove();
            $table.find('th').removeAttr('rowspan');
            if (!self.showHeader) {
                $table.find('thead').remove();
            }
            if (!self.showPageSummary) {
                $table.find('tfoot.kv-page-summary').remove();
            }
            if (!self.showFooter) {
                $table.find('tfoot.kv-table-footer').remove();
            }
            if (!self.showCaption) {
                $table.find('kv-table-caption').remove();
            }
            $table.find('.skip-export').remove();
            $table.find('.skip-export-' + $type).remove();
            var htmlContent = $table.html();
            htmlContent = self.preProcess(htmlContent);
            $table.html(htmlContent);
            return $table;
        },
        preProcess: function(content) {
            var self = this, conv = self.exportConversions, l = conv.length, processed = content;
            if (l > 0) {
                for (var i = 0; i < l; i++) {
                    processed = replaceAll(processed, conv[i]['from'], conv[i]['to']);
                }
            }
            return processed;
        },
        download: function (type, content) {
            var self = this;
            self.$form.find('[name="export_filetype"]').val(type);
            self.$form.find('[name="export_filename"]').val(self.filename);
            self.$form.find('[name="export_content"]').val(content);
            self.$form.submit();
        },
        exportHTML: function () {
            var self = this, $table = self.clean('html');
            var css = (self.cssFile && self.cssFile.length) ? '<link href="' + self.cssFile + '" rel="stylesheet">' : '';
            var html = HTML_TEMPLATE.replace('{css}', css).replace('{data}', $('<div />').html($table.clone()).html());
            self.download('html', html);
        },
        exportTEXT: function ($type) {
            var self = this, $table = self.clean($type);
            var $rows = $table.find('tr:has(' + self.columns + ')');
            // Temporary delimiter characters unlikely to be typed by keyboard
            // This is to avoid accidentally splitting the actual contents
            var tmpColDelim = String.fromCharCode(11), // vertical tab character
                tmpRowDelim = String.fromCharCode(0); // null character

            // actual delimiter characters for CSV format
            var colDelim = '"' + self.colDelimiter + '"', rowDelim = '"' + self.rowDelimiter + '"';
            // Grab text from table into CSV formatted string
            var txt = '"' + $rows.map(function (i, row) {
                var $row = $(row), $cols = $row.find(self.columns);
                return $cols.map(function (j, col) {
                    var $col = $(col), text = $col.text();
                    return text.replace('"', '""'); // escape double quotes
                }).get().join(tmpColDelim);
            }).get().join(tmpRowDelim)
                .split(tmpRowDelim).join(rowDelim)
                .split(tmpColDelim).join(colDelim) + '"';
            self.download($type, txt);
        },
        exportEXCEL: function () {
            var self = this, $table = self.clean('xls');
            $table.find('input').remove(); // remove any form inputs as they do not align well in excel
            var css = (self.cssFile && self.cssFile.length) ? '<link href="' + self.cssFile + '" rel="stylesheet">' : '';
            var xls = EXCEL_TEMPLATE.replace('{css}', css).replace('{worksheet}', self.worksheet).replace('{data}', $('<div />').html($table.clone()).html()).replace(/"/g, '\'');
            self.download('xls', xls);
        },
    };

    $.fn.gridexport = function (options) {
        return this.each(function () {
            var $this = $(this), data = $this.data('gridexport')
            if (!data) {
                $this.data('gridexport', (data = new GridExport(this, options)))
            }
            if (typeof options == 'string') {
                data[options]()
            }
        })
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
                $this.data('gridexport', (data = new GridExport(this, $.extend({}, $.fn.gridexport.defaults, options, $(this).data()))));
            }

            if (typeof option === 'string') {
                data[option].apply(data, args);
            }
        });
    };

    $.fn.gridexport.defaults = {
        filename: 'export',
        showHeader: true,
        showPageSummary: true,
        showFooter: true,
        showCaption: true,
        worksheet: '',
        colDelimiter: ',',
        rowDelimiter: '\r\n',
        alertMsg: '',
        browserPopupsMsg: '',
        cssFile: '',
        exportConversions: {}
    };

})(window.jQuery);