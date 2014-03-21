<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2013
 * @package yii2-widgets
 * @version 1.0.0
 */

namespace kartik\grid;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

/**
 * A BooleanColumn to convert true/false values as user friendly indicators
 * with an automated drop down filter for the Grid widget [[\kartik\widgets\GridView]]
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class BooleanColumn extends DataColumn
{

	/**
	 * @var string the width of each column (matches the CSS width property).
	 * Defaults to `80px`.
	 * @see http://www.w3schools.com/cssref/pr_dim_width.asp
	 */
	public $width = '90px';

	/**
	 * @var string the horizontal alignment of each column. Should be one of
	 * 'left', 'right', or 'center'. Defaults to `center`.
	 */
	public $hAlign = 'center';

	/**
	 * @var string label for the true value. Defaults to `On`.
	 */
	public $trueLabel = 'On';

	/**
	 * @var string label for the false value. Defaults to `Off`.
	 */
	public $falseLabel = 'Off';

	/**
	 * @var string icon/indicator for the true value. If this is not set, it will use the value from `trueLabel`.
	 * If GridView `bootstrap` parameter is set to true - it will default to
	 * `<span class="glyphicon glyphicon-ok text-success"></span>`
	 */
	public $trueIcon;

	/**
	 * @var string|array in which format should the value of each data model be displayed
	 * Defaults to `raw`.
	 * [[\yii\base\Formatter::format()]] or [[\yii\i18n\Formatter::format()]] is used.
	 */
	public $format = 'raw';
	/**
	 * @var string icon/indicator for the false value. If this is null, it will use the value from `falseLabel`.
	 * If GridView `bootstrap` parameter is set to true - it will default to
	 * `<span class="glyphicon glyphicon-remove text-danger"></span>`
	 */
	public $falseIcon;

	public function init()
	{
		$this->filter = [true => $this->trueLabel, false => $this->falseLabel];
		if ($this->grid->bootstrap) {
			$this->trueIcon = !isset($this->trueIcon) ? '<span class="glyphicon glyphicon-ok text-success"></span>' : $this->trueLabel;
			$this->falseIcon = !isset($this->falseIcon) ? '<span class="glyphicon glyphicon-remove text-danger"></span>' : $this->falseLabel;
		} else {
			$this->trueIcon = $this->trueLabel;
			$this->falseIcon = $this->falseLabel;
		}
		parent::init();
	}

	protected function getDataCellContent($model, $key, $index)
	{
		$value = parent::getDataCellContent($model, $key, $index);
		if ($value !== null) {
			return $value ? $this->trueIcon : $this->falseIcon;
		}
		return $value;
	}
}