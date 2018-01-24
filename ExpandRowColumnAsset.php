<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2018
 * @version   3.1.8
 */

namespace kartik\grid;

use kartik\base\AssetBundle;

/**
 * Asset bundle for [[ExpandRowColumn]] functionality of the [[GridView]] widget.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class ExpandRowColumnAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('js', ['js/kv-grid-expand']);
        $this->setupAssets('css', ['css/kv-grid-expand']);
        parent::init();
    }
}


