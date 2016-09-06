<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2016
 * @version   3.1.3
 */

namespace kartik\grid;

use Yii;

/**
 * This module allows global level configurations for the enhanced Krajee [[GridView]]. One can configure the module
 * in their Yii configuration file as shown below:
 *
 * ```php
 * 'modules' => [
 *     'gridview' => [
 *          'class' => 'kartik\grid\Module',
 *          'downloadAction' => '/gridview/export/download' // your grid export download setting
 *     ]
 * ]
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class Module extends \kartik\base\Module
{
    /**
     * The module name for Krajee gridview
     */
    const MODULE = "gridview";

    /**
     * @var string|array the action (url) used for downloading exported file
     */
    public $downloadAction = '/gridview/export/download';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_msgCat = 'kvgrid';
        parent::init();
        if (isset($dummyDemoTranslations)) {
            /** @noinspection PhpUnusedLocalVariableInspection */
            $dummyMessages = Yii::t('kvgrid', 'Add Book') .
                Yii::t('kvgrid', 'Book Listing') .
                Yii::t('kvgrid', 'Download Selected') .
                Yii::t('kvgrid', 'Library') .
                Yii::t('kvgrid', 'Reset Grid') .
                Yii::t('kvgrid', 'The page summary displays SUM for first 3 amount columns and AVG for the last.') .
                Yii::t('kvgrid', 'The table header sticks to the top in this demo as you scroll') .
                Yii::t('kvgrid', 'Resize table columns just like a spreadsheet by dragging the column edges.');
        }
    }
}
