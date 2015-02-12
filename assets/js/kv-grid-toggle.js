/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2015
 * @version   3.0.0
 *
 * Client actions for yii2-grid toggle data
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2015, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
var kvToggleGridData = function (id) {
    "use strict";
    (function ($) {
        var $el = $('#' + id);
        if ($el.length === 0) {
            return;
        }
        $el.on('change', function () {
            var $form = $el.closest('form');
            $form.submit();
        });
    })(window.jQuery);
};
