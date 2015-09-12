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
 */var kvSelectRow=function(n,t){"use strict";!function(i){var e=i("#"+n),c=function(n,i){var e=n.closest("tr"),c=i||n;c.is(":checked")&&!n.attr("disabled")?e.removeClass(t).addClass(t):e.removeClass(t)},o=function(n,t){return t===!0?void e.find(".kv-row-select input").each(function(){c(i(this),n)}):void c(n)};e.find(".kv-row-select input").on("change",function(){o(i(this))}).each(function(){o(i(this))}),e.find(".kv-all-select input").on("change",function(){o(i(this),!0)})}(window.jQuery)};