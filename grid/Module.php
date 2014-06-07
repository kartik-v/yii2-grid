<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2013
 * @package yii2-grid
 * @version 1.0.0
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
    public $downloadAction = 'gridview/export/download';

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
        if(empty(Yii::$app->i18n->translations['kvgrid'])) {
            Yii::$app->i18n->translations['kvgrid'] = $this->i18n;
        }
    }
}
