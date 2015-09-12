/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2015
 * @version   3.0.7
 *
 * Client actions for yii2-grid CheckboxColumn
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2015, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
var kvSelectRow = function (gridId, css) {
    "use strict";
    (function ($) {
        var $grid = $('#' + gridId), $el,
            highlight = function($el, $parent) {
                var $row = $el.closest('tr'), $cbx = $parent || $el;
                if ($cbx.is(':checked') && !$el.attr('disabled')) {
                    $row.removeClass(css).addClass(css);
                } else {
                    $row.removeClass(css);
                }
            },
            toggle = function($cbx, all) {
                var $row;
                if (all === true) {
                    $grid.find(".kv-row-select input").each(function() {
                        highlight($(this), $cbx);
                    });
                    return;
                }
                highlight($cbx);
            };
        $grid.find(".kv-row-select input").on('change', function () {
            toggle($(this));
        }).each(function() {
            toggle($(this));            
        });
        $grid.find(".kv-all-select input").on('change', function () {
            toggle($(this), true);
        });
    })(window.jQuery);
};