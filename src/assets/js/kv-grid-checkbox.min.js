/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   3.3.4
 *
 * Client actions for yii2-grid CheckboxColumn
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2014 - 2019, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */var kvSelectRow,kvSelectColumn;!function(e){"use strict";kvSelectRow=function(n,i){var c="krajeeGrid",t="change."+c,l=e("#"+n),o=l.find(".kv-row-select input"),a=function(e,n){var c=e.closest("tr"),t=n||e;t.is(":checked")&&!e.attr("disabled")?c.removeClass(i).addClass(i):c.removeClass(i)},d=function(){o.each(function(){a(e(this))})};o.off(t).on(t,function(){a(e(this))}),l.find(".kv-all-select input").off(t).on(t,function(e){void 0===e.namespace&&e.handleObj.namespace===c&&setTimeout(function(){d()},100)}),d()},kvSelectColumn=function(n,i){var c,t,l,o="#"+n,a=e(o);i.multiple&&i.checkAll&&(c=o+" input[name='"+i.checkAll+"']",t=i["class"]?"input."+i["class"]:"input[name='"+i.name+"']",l=o+" "+t+":enabled",e(document).off("click.yiiGridView",c).on("click.yiiGridView",c,function(){a.find(t+":enabled").prop("checked",this.checked)}),e(document).off("click.yiiGridView",l).on("click.yiiGridView",l,function(){var e=a.find(t).length===a.find(t+":checked").length;a.find("input[name='"+i.checkAll+"']").prop("checked",e)}))}}(window.jQuery);