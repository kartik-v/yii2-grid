/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2020
 * @version   3.3.5
 *
 * Client actions for yii2-grid EditableColumn
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2014 - 2020, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */var kvRefreshEC;!function(i){"use strict";kvRefreshEC=function(e,n){var t=i("#"+e);t.find("."+n).each(function(){i(this).on("editableSuccess",function(){t.yiiGridView("applyFilter")})})}}(window.jQuery);