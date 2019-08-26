<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   3.3.4
 */

namespace kartik\grid;

use Yii;

/**
 * Dummy demo class used for generating translation messages for the grid demo.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since  1.0
 */
class Demo
{
    /**
     * Demo messages
     */
    public function messages()
    {
        return [
            Yii::t('kvgrid', 'Add Book'),
            Yii::t('kvgrid', 'Book Listing'),
            Yii::t('kvgrid', 'Download Selected'),
            Yii::t('kvgrid', 'Library'),
            Yii::t('kvgrid', 'Reset Grid'),
            Yii::t('kvgrid', 'The page summary displays SUM for first 3 amount columns and AVG for the last.'),
            Yii::t('kvgrid', 'The table header sticks to the top in this demo as you scroll'),
            Yii::t('kvgrid', 'Resize table columns just like a spreadsheet by dragging the column edges.')
        ];
    }
}
