<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2022
 * @version   3.5.0
 */

namespace kartik\grid;

/**
 * This module allows global level configurations for the enhanced Krajee [[GridView]].
 *
 * Setup the module in your Yii configuration file with a name `gridview` as shown below. Refer the
 * [yii2-grid documentation](http://demos.krajee.com/grid#module) for details on the gridview module
 * configuration parameters supported.
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
 * @since  1.0
 */
class Module extends \kartik\base\Module
{
    /**
     * The module name for Krajee gridview
     */
    const MODULE = "gridview";

    /**
     * @var string a random salt that will be used to generate a hash string for export configuration.
     */
    public $exportEncryptSalt = 'SET_A_SALT_FOR_YII2_GRID';

    /**
     * @var string|array the action (url) used for downloading exported file
     */
    public $downloadAction;

    /**
     * @inheritdoc
     */
    protected $_msgCat = 'kvgrid';
}
