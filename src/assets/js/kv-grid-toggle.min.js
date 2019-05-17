/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   3.3.2
 *
 * jQuery methods library for yii2-grid toggle data
 * 
 * Author: Kartik Visweswaran
 * Copyright: Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */var kvToggleData;!function(i){"use strict";kvToggleData=function(e){i("#"+e.id).off("click").on("click",function(t,r){var c=i(this);return r&&r.redirect?void(e.pjax||window.location.replace(c.attr("href"))):void("page"===e.mode&&(t.preventDefault(),e.lib.confirm(e.msg,function(i){i&&c.trigger("click",{redirect:!0})})))})}}(window.jQuery);