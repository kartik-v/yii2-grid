/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2015
 * @version   3.0.7
 *
 * Client actions for yii2-grid EditableColumn
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2015, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
var kvRefreshEC = function (gridId, css) {
    "use strict";
    (function ($) {
        var $grid = $('#' + gridId);
        $grid.find('.' + css).each(function() {
            $(this).on('editableSuccess', function(){
                $grid.yiiGridView("applyFilter");
            });
        });
    })(window.jQuery);
};