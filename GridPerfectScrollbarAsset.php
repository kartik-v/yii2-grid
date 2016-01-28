<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2016
 * @version   3.1.1
 */

namespace kartik\grid;

use kartik\base\AssetBundle;

/**
 * Asset bundle for GridView Widget (for perfect scrollbar)
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
        $this->setSourcePath(__DIR__ . '/assets');
        $this->setupAssets('css', ['css/perfect-scrollbar', 'css/perfect-scrollbar-kv']);
        $this->setupAssets('js', ['js/perfect-scrollbar.jquery']);
        parent::init();
    }
}
