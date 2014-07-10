<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @package yii2-grid
 * @version 1.6.0
 */

namespace kartik\grid;

use kartik\widgets\AssetBundle;

/**
 * Asset bundle for GridView Widget
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class GridViewAsset extends AssetBundle
{

	public function init()
	{
		$this->setSourcePath(__DIR__ . '/assets');
		$this->setupAssets('js', ['js/kv-grid']);
		$this->setupAssets('css', ['css/kv-grid']);
		parent::init();
	}

}