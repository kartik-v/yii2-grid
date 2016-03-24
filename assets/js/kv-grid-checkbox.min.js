/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2016
 * @version   3.1.1
 *
 * Client actions for yii2-grid CheckboxColumn
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2015, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */var kvSelectRow;!function(n){"use strict";kvSelectRow=function(t,e){var i=n("#"+t),c=function(n,t){var i=n.closest("tr"),c=t||n;c.is(":checked")&&!n.attr("disabled")?i.removeClass(e).addClass(e):i.removeClass(e)},o=function(t,e){return e===!0?void i.find(".kv-row-select input").each(function(){c(n(this),t)}):void c(t)};i.find(".kv-row-select input").on("change",function(){o(n(this))}).each(function(){o(n(this))}),i.find(".kv-all-select input").on("change",function(){o(n(this),!0)})}}(window.jQuery);