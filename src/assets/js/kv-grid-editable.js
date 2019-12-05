/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   3.3.5
 *
 * Client actions for yii2-grid EditableColumn
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2014 - 2019, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
var kvRefreshEC;
(function ($) {
    "use strict";
    kvRefreshEC = function (gridId, css) {
        var $grid = $('#' + gridId);
        $grid.find('.' + css).each(function () {
            $(this).on('editableSuccess', function () {
                $grid.yiiGridView("applyFilter");
            });
        });
    };
})(window.jQuery);