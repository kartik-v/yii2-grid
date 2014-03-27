/*!
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2013
 * @version 1.0.0
 *
 * Grid Export Validation Module for Yii's Gridview. Supports export of
 * grid data as CSV, HTML, or Excel.
 *
 * Author: Kartik Visweswaran
 * Copyright: 2013, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
(function ($) {
    var GridExport = function (element, options) {
        this.$element = $(element);
        this.$grid = options.grid;
        this.$table = this.$grid.find('table');
        this.$form = this.$grid.find('form.kv-export-form');
        this.filename = options.filename;
        this.showHeader = options.showHeader;
        this.columns = options.showHeader ? 'td,th' : 'td';
        this.htmlTemplate = options.htmlTemplate;
        this.worksheet = options.worksheet;
	    this.colDelimiter = options.colDelimiter;
	    this.rowDelimiter = options.rowDelimiter;
		this.message = options.message;
        this.listen();
    };

    GridExport.prototype = {
        constructor: GridExport,
        listen: function () {
            var self = this;
            if (self.$element.hasClass('export-csv')) {
                self.$element.on("click", function (e) {
                    if (self.message && self.message.length) {
                        alert(self.message);
                    }
                    self.exportTEXT('csv');
                    e.preventDefault();
                });
            }
	        else if (self.$element.hasClass('export-txt')) {
		        self.$element.on("click", function (e) {
			        if (self.message && self.message.length) {
				        alert(self.message);
			        }
			        self.exportTEXT('txt');
			        e.preventDefault();
		        });
	        }
            else if (self.$element.hasClass('export-html')) {
                self.$element.on("click", function (e) {
                    if (self.message && self.message.length) {
                        alert(self.message);
                    }
                    self.exportHTML();
                    e.preventDefault();
                });
            }
            else if (self.$element.hasClass('export-xls')) {
                self.$element.on("click", function (e) {
                    if (self.message && self.message.length) {
                        alert(self.message);
                    }
                    self.exportEXCEL();
                    e.preventDefault();
                });
            }
        },
        clean: function () {
            var self = this, $table = self.$table;
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
            return $table;
        },
        download: function (type, content) {
            var self = this;
            self.$form.find('[name="export_filetype"]').val(type);
            self.$form.find('[name="export_filename"]').val(self.filename);
            self.$form.find('[name="export_content"]').val(content);
            self.$form.submit();
        },
        exportHTML: function () {
            var self = this, $table = self.clean();
            var html = self.htmlTemplate.replace('{data}', $('<div />').html($table.clone()).html());
            self.download('html', html);
        },
        exportTEXT: function ($type) {
            var self = this, $table = self.clean(), $rows = $table.find('tr:has(' + self.columns + ')');
	        // Temporary delimiter characters unlikely to be typed by keyboard
            // This is to avoid accidentally splitting the actual contents
            var tmpColDelim = String.fromCharCode(11), // vertical tab character
                tmpRowDelim = String.fromCharCode(0); // null character

            // actual delimiter characters for CSV format
            var colDelim = self.colDelimiter, rowDelim = self.rowDelimiter;
			var quote = ($type == 'csv') ? '"' : '';
            // Grab text from table into CSV formatted string
            var output = quote + $rows.map(function (i, row) {
                var $row = $(row), $cols = $row.find(self.columns);
                return $cols.map(function (j, col) {
                    var $col = $(col), text = $col.text();
                    return text.replace('"', '""'); // escape double quotes
                }).get().join(tmpColDelim);
            }).get().join(tmpRowDelim)
                .split(tmpRowDelim).join(rowDelim)
                .split(tmpColDelim).join(colDelim) + quote;
            self.download($type, output);
        },
        exportEXCEL: function () {
            var self = this, $table = self.clean();
            var xls = $('<div />').html($table.clone()).html();
            xls = self.htmlTemplate.replace('{worksheet}', self.worksheet).replace('{data}', xls);
            xls = xls.replace(/"/g, '\'');
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
        htmlTemplate: '',
        worksheet: '',
	    colDelimiter: ',',
	    rowDelimiter: '\r\n',
        message: ''
    };

})(window.jQuery);