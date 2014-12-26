/*!
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @version 2.9.0
 *
 * Client actions for yii2-grid toggle data
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2014, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
(function ($) {
    kvToggleGridData = function(id) {
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
