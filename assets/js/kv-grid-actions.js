/*!
 * @package   yii2-grid
 * @author    Yasser Hassan <yhassan@yahoo.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2015
 * @version   3.0.1
 *
 * Client actions for yii2-grid ActionColumn
 * 
 * Author: Yasser Hassan
 * Copyright: 2015, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
var kvDeleteRow = function(gridId, callback) {
    "use strict";
    (function($) {
        var $grid = $('#'+gridId);
        $grid.find("a[data-delete]").on('click', function(e) {
            //$el = $(this);
            var message = $(this).data('delete');
            if(confirm(message)) {
                var $row = $(this).parents("tr:first");
                var key = $row.data('key');
                $row.remove();
                if(callback && typeof(callback) === 'function') {
                    callback(key);
                }
            }
            e.preventDefault();
        });
    })(window.jQuery);
};
