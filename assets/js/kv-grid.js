/*!
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @version 2.4.0
 *
 * Client actions for yii2-grid 
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2013, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
(function ($) {
    selectRow = function(gridId, css) {
        var $grid = jQuery('#' + gridId);
        $grid.find(".kv-row-select input").on('change', function () {
            var $el = $(this);
            if ($el.is(':checked')) {
                $el.parents("tr:first").removeClass(css).addClass(css);
            } else {
                $el.parents("tr:first").removeClass(css);
            }
        });
        $grid.find(".kv-all-select input").on('change', function () {
            if ($(this).is(':checked')) {
                $grid.find(".kv-row-select").parents("tr").removeClass(css).addClass(css);
            }
            else {
                $grid.find(".kv-row-select").parents("tr").removeClass(css);
            }
        });
    }

    toggleGridData = function(id) {
        var $el = $('#' + id);
        if ($el.length == 0) {
            return;
        }
        $el.on('change', function() {
            var $form = $el.closest('form');
            $form.submit();
        });
    }
})(window.jQuery);
