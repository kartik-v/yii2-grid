/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2017
 * @version   3.1.4
 *
 * Client actions for kartik\grid\ActionColumn
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2015, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */var kvActionDelete;!function(e){"use strict";kvActionDelete=function(t){e("."+t.css).off("click.krajee").on("click.krajee",function(o,r){var n=e(this),a=n.closest("tr"),c=n.closest("td"),l=window[t.lib];r=r||{},r.proceed||(o.stopPropagation(),o.preventDefault(),l.confirm(t.msg,function(o){o&&(t.pjax?e.ajax({url:n.attr("href"),type:"post",beforeSend:function(){a.addClass("kv-delete-row"),c.addClass("kv-delete-cell")},complete:function(){a.removeClass("kv-delete-row"),c.removeClass("kv-delete-cell")},error:function(e){l.alert("There was an error with your request."+e.responseText)}}).done(function(){e.pjax.reload({container:"#"+t.pjaxContainer})}):n.data("method","post").trigger("click",{proceed:!0}))}))})}}(window.jQuery);