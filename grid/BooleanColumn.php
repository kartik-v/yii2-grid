<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2013
 * @package yii2-grid
 * @version 1.0.0
 */

namespace kartik\grid;

use Yii;
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
     * @var string the horizontal alignment of each column. Should be one of
     * 'left', 'right', or 'center'. Defaults to `center`.
     */
    public $hAlign = 'center';

    /**
     * @var string the width of each column (matches the CSS width property).
     * Defaults to `90px`.
     * @see http://www.w3schools.com/cssref/pr_dim_width.asp
     */
    public $width = '90px';

    /**
     * @var string|array in which format should the value of each data model be displayed
     * Defaults to `raw`.
     * [[\yii\base\Formatter::format()]] or [[\yii\i18n\Formatter::format()]] is used.
     */
    public $format = 'raw';

    /**
     * @var boolean|string|Closure the page summary that is displayed above the footer.
     * Defaults to false.
     */
    public $pageSummary = false;

    /**
     * @var string label for the true value. Defaults to `Active`.
     */
    public $trueLabel;

    /**
     * @var string label for the false value. Defaults to `Inactive`.
     */
    public $falseLabel;

    /**
     * @var string icon/indicator for the true value. If this is not set, it will use the value from `trueLabel`.
     * If GridView `bootstrap` property is set to true - it will default to
     * `<span class="glyphicon glyphicon-ok text-success"></span>`
     */
    public $trueIcon;

    /**
     * @var string icon/indicator for the false value. If this is null, it will use the value from `falseLabel`.
     * If GridView `bootstrap` property is set to true - it will default to
     * `<span class="glyphicon glyphicon-remove text-danger"></span>`
     */
    public $falseIcon;

    public function init()
    {
        if (empty($this->trueLabel)) {
            $this->trueLabel = Yii::t('kvgrid', 'Active');
        }
        if (empty($this->falseLabel)) {
            $this->falseLabel = Yii::t('kvgrid', 'Inactive');
        }
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

    public function getDataCellValue($model, $key, $index)
    {
        $value = parent::getDataCellValue($model, $key, $index);
        if ($value !== null) {
            return $value ? $this->trueIcon : $this->falseIcon;
        }
        return $value;
    }
}
