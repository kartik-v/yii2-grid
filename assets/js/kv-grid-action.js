/*!
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2017
 * @version   3.1.5
 *
 * Client actions for kartik\grid\ActionColumn
 * 
 * Author: Kartik Visweswaran
 * Copyright: 2015, Kartik Visweswaran, Krajee.com
 * For more JQuery plugins visit http://plugins.krajee.com
 * For more Yii related demos visit http://demos.krajee.com
 */
var kvActionDelete;
(function ($) {
    "use strict";
    kvActionDelete = function (opts) {
        $('.' + opts.css).off('click.krajee').on('click.krajee', function (e, options) {
            var $btn = $(this), $row = $btn.closest('tr'), $cell = $btn.closest('td'), lib = window[opts.lib];
            options = options || {};
            if (!options.proceed) {
                e.stopPropagation();
                e.preventDefault();
                lib.confirm(opts.msg, function (result) {
                    if (!result) {
                        return;
                    }
                    if (opts.pjax) {
                        $.ajax({
                            url: $btn.attr('href'),
                            type: 'post',
                            beforeSend: function() {
                                $row.addClass('kv-delete-row');
                                $cell.addClass('kv-delete-cell');
                            },
                            complete: function () {
                                $row.removeClass('kv-delete-row');
                                $cell.removeClass('kv-delete-cell');
                            },
                            error: function (xhr, status, error) {
                                lib.alert('There was an error with your request.' + xhr.responseText);
                            }
                        }).done(function (data) {
                            $.pjax.reload({container: '#' + opts.pjaxContainer});
                        });
                    } else {
                        $btn.data('method', 'post').trigger('click', {proceed: true});
                    }
                });
            }
        });
    };
})(window.jQuery);