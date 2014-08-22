/*!
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @version 1.9.0
 *
 * Client actions for yii2-grid 
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2013, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */

var selectRow = function(gridId, css) {
    var $grid = jQuery('#' + gridId);
    $grid.find(".kv-row-select input").on('change', function () {
        $(this).parents("tr:first").toggleClass(css);
    });
    $grid.find(".kv-all-select input").on('change', function () {
        if ($(this).is(':checked')) {
            $grid.find(".kv-row-select").parents("tr").addClass(css);
        }
        else {
            $grid.find(".kv-row-select").parents("tr").removeClass(css);
        }
    });
}

var initCheckbox = function (pjaxContainer, gridId, css, initPjaxVar) {
    $('#' + pjaxContainer).on("pjax:complete", function () {
        if (window[initPjaxVar] !== true) {
            selectRow(gridId, css);
            window[initPjaxVar] = true;
        }
    });
};
