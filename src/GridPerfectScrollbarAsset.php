<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2021
 * @version   3.3.6
 */

namespace kartik\grid;

use kartik\base\AssetBundle;

/**
 * Asset bundle for perfect scrollbar functionality for the [[GridView]] widget.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class GridPerfectScrollbarAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->depends = array_merge(["kartik\\grid\\GridViewAsset"], $this->depends);
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('css', ['css/perfect-scrollbar']);
        $this->setupAssets('js', ['js/perfect-scrollbar']);
        parent::init();
    }
}
