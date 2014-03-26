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
		this.filename = options.filename;
		this.showHeader = options.showHeader;
		this.columns = options.showHeader ? 'td,th' : 'td';
		this.htmlTemplate = options.htmlTemplate;
		this.hasDownloadSupport = ("download" in document.createElement("a"));
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
					self.exportCSV();
					if (!self.hasDownloadSupport) {
						e.preventDefault();
					}
				});
			}
			else if (self.$element.hasClass('export-html')) {
				self.$element.on("click", function (e) {
					if (self.message && self.message.length) {
						alert(self.message);
					}
					self.exportHTML();
					if (!self.hasDownloadSupport) {
						e.preventDefault();
					}
				});
			}
		},
		exportHTML: function () {
			var self = this, $table = self.$table;

			// Skip the filter rows and header rowspans
			$table.find('tr.filters').remove();
			$table.find('th').removeAttr('rowspan');
			var htmlData = $('<div />').html($table.clone()).html(),
				htmlData = 'data:x-application/text;charset=utf-8,' + encodeURIComponent(self.htmlTemplate.replace('{data}', htmlData));
			if (self.hasDownloadSupport) {
				self.$element.attr({
					'download': self.filename + '.htm',
					'href': htmlData,
					'target': '_blank'
				});
			}
			else {
				window.open(htmlData);
			}
		},
		exportCSV: function () {
			var self = this, $rows = self.$table.find('tr:has(' + self.columns + ')');

			// Temporary delimiter characters unlikely to be typed by keyboard
			// This is to avoid accidentally splitting the actual contents
			var tmpColDelim = String.fromCharCode(11), // vertical tab character
				tmpRowDelim = String.fromCharCode(0); // null character

			// actual delimiter characters for CSV format
			var colDelim = '","', rowDelim = '"\r\n"';

			// Grab text from table into CSV formatted string
			var csv = '"' + $rows.map(function (i, row) {
				var $row = $(row), $cols = $row.find(self.columns);
				return $cols.map(function (j, col) {
					var $col = $(col), text = $col.text();
					return text.replace('"', '""'); // escape double quotes
				}).get().join(tmpColDelim);
			}).get().join(tmpRowDelim)
				.split(tmpRowDelim).join(rowDelim)
				.split(tmpColDelim).join(colDelim) + '"';

			// Data URI
			var csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

			if (self.hasDownloadSupport) {
				self.$element.attr({
					'download': self.filename,
					'href': csvData,
					'target': '_blank'
				});
			}
			else {
				window.open(csvData);
			}
		}
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
		htmlTemplate: '',
		hasDownloadSupport: true,
		message: ''
	};

})(window.jQuery);