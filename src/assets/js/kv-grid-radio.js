/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2022
 * @version   3.5.0
 *
 * Client actions for yii2-grid RadioColumn
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2014 - 2022, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
var kvClearRadioRow, kvSelectRadio, kvClearRadio;
(function ($) {
    "use strict";
    kvClearRadioRow = function ($grid, css) {
        if (css.length) {
            $grid.find('.kv-row-radio-select').each(function () {
                $(this).closest('tr').removeClass(css);
            });
        }
    };
    kvSelectRadio = function (gridId, name, css) {
        css = css || '';
        var $grid = $('#' + gridId), $radio = $grid.find("input[name='" + name + "']"), $el, key;
        $radio.on('change', function () {
            $el = $(this);
            kvClearRadioRow($grid, css);
            if ($el.is(':checked')) {
                var $row = $el.parent().closest('tr'), key = $row.data('key');
                if (css.length) {
                    $row.addClass(css);
                }
                $grid.trigger('grid.radiochecked', [key, $el.val()]);
            }
        });
    };
    kvClearRadio = function (gridId, name, css) {
        css = css || '';
        var $grid = $('#' + gridId), key, val, $radio;
        $grid.find(".kv-clear-radio").on('click', function () {
            $radio = $grid.find("input[name='" + name + "']:checked");
            if (!$radio || !$radio.length) {
                return;
            }
            key = $radio.parent().closest('tr').data('key');
            val = $radio.val();
            $radio.prop('checked', false);
            kvClearRadioRow($grid, css);
            $grid.trigger('grid.radiocleared', [key, val]);
        });
    };
})(window.jQuery);