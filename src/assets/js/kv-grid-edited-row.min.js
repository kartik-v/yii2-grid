/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2022
 * @version   3.5.0
 *
 * Client actions for yii2-grid edited row
 *
 * Author: Kartik Visweswaran
 * Copyright: 2014 - 2022, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */var kvGridEditedRow;!function(t){"use strict";kvGridEditedRow=function(e){var s,d=e.grid,o=t("#"+d),i=e.row,r=e.css;i&&(s=o.find("tr."+d+'[data-key="'+e.row+'"]'),s.length&&(s.removeClass(r).addClass(r),s[0].scrollIntoView({block:"center"}))),t(".enable-edited-row").on("click",function(){t(this).closest("table").find("> tbody > tr").removeClass(r),t(this).closest("tr").addClass(r)})}}(window.jQuery);