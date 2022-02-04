/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2022
 * @version   3.5.0
 *
 * Client actions for yii2-grid edited row
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2014 - 2022, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
var kvGridEditedRow;
(function ($) {
    "use strict";
    kvGridEditedRow = function (options) {
        var id = options.grid, $grid = $('#' + id), row = options.row, css = options.css, $row;
        if (row) {
            $row = $grid.find('tr.' + id + '[data-key="' + options.row + '"]');
            if ($row.length) {
                $row.removeClass(css).addClass(css);
                $row[0].scrollIntoView({block: "center"});
            }
        }
        $('.enable-edited-row').on('click', function() {
            $(this).closest('table').find('> tbody > tr').removeClass(css);
            $(this).closest('tr').addClass(css);
        });
    };
})(window.jQuery);