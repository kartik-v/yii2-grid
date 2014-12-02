<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @package yii2-grid
 * @version 2.5.0
 */

namespace kartik\grid;

use Yii;

/**
 * Module with various modifications to the Yii 2 grid.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class Module extends \yii\base\Module
{
    /**
     * @var mixed the action (url) used for downloading exported file
     */
    public $downloadAction = '/gridview/export/download';
    
    /**
     * @var array the the internalization configuration for this module
     */
    public $i18n = [];

    public function init()
    {
        parent::init();
        $this->initI18N();

    }

    public function initI18N()
    {
        Yii::setAlias('@kvgrid', dirname(__FILE__));
        if (empty($this->i18n)) {
            $this->i18n = [
                'class' => 'yii\i18n\PhpMessageSource',
                'basePath' => '@kvgrid/messages',
                'forceTranslation' => true
            ];
        }
        Yii::$app->i18n->translations['kvgrid'] = $this->i18n;
        if (isset($dummyDemoTranslations)) {
            $messages = Yii::t('kvgrid', 'Add Book') .
                Yii::t('kvgrid', 'Book Listing') .
                Yii::t('kvgrid', 'Download Selected') .
                Yii::t('kvgrid', 'Library') .
                Yii::t('kvgrid', 'Reset Grid') .
                Yii::t('kvgrid', 'The page summary displays SUM for first 3 amount columns and AVG for the last.') .
                Yii::t('kvgrid', 'The table header sticks to the top in this demo as you scroll');
        }
    }
}