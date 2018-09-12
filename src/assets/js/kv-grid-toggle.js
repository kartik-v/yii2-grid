/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2018
 * @version   3.2.1
 *
 * jQuery methods library for yii2-grid toggle data
 * 
 * Author: Kartik Visweswaran
 * Copyright: Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
var kvToggleData;
(function ($) {
    "use strict";
    kvToggleData = function (options) {
        $('#' + options.id).off('click').on('click', function (e, params) {
            var $btn = $(this);
            if (params && params.redirect) {
                if (!options.pjax) {
                    window.location.replace($btn.attr('href'));
                }
                return;
            }
            if (options.mode === 'page') {
                e.preventDefault();
                options.lib.confirm(options.msg, function (result) {
                    if (result) {
                        $btn.trigger('click', {redirect: true});
                    }
                });
            }
        });
    };
})(window.jQuery);