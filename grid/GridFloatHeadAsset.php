<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @package yii2-grid
 * @version 1.6.0
 */

namespace kartik\grid;

use kartik\widgets\AssetBundle;

/**
 * Asset bundle for GridView Widget (for floating header)
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class GridFloatHeadAsset extends AssetBundle
{

	public function init()
	{
		$this->setSourcePath(__DIR__ . '/assets');
		$this->setupAssets('js', ['js/jquery.floatThead']);
		parent::init();
	}

}