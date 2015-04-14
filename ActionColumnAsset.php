<?php

/**
 * @package   yii2-grid
 * @author    Yasser Hassan <yhassan@yahoo.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2015
 * @version   3.0.1
 */

namespace kartik\grid;

/**
 * Asset bundle for GridView ActionColumn
 *
 * @author Yasser Hassan <yhassan@yahoo.com>
 * @since
 */
class ActionColumnAsset extends \kartik\base\AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init() {
        $this->setSourcePath(__DIR__.'/assets');
        $this->setupAssets('js', ['js/kv-grid-actions']);
        parent::init();
    }
}
