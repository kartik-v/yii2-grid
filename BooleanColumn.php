<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2016
 * @version   3.1.0
 */

namespace kartik\grid;

use Yii;
use Closure;

/**
 * A BooleanColumn to convert true/false values as user friendly indicators with an automated drop down filter for the
 * Grid widget [[\kartik\widgets\GridView]]
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class BooleanColumn extends DataColumn
{
    /**
     * @var string the horizontal alignment of each column. Should be one of 'left', 'right', or 'center'. Defaults to
     *     `center`.
     */
    public $hAlign = 'center';

    /**
     * @var string the width of each column (matches the CSS width property). Defaults to `90px`.
     * @see http://www.w3schools.com/cssref/pr_dim_width.asp
     */
    public $width = '90px';

    /**
     * @var string|array in which format should the value of each data model be displayed. Defaults to `raw`.
     * [[\yii\base\Formatter::format()]] or [[\yii\i18n\Formatter::format()]] is used.
     */
    public $format = 'raw';

    /**
     * @var boolean|string|Closure the page summary that is displayed above the footer. Defaults to false.
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
     * @var string icon/indicator for the true value. If this is not set, it will use the value from `trueLabel`. If
     *     GridView `bootstrap` property is set to true - it will default to [[GridView::ICON_ACTIVE]] `<span
     *     class="glyphicon glyphicon-ok text-success"></span>`
     */
    public $trueIcon;

    /**
     * @var string icon/indicator for the false value. If this is null, it will use the value from `falseLabel`. If
     *     GridView `bootstrap` property is set to true - it will default to [[GridView::ICON_INACTIVE]] `<span
     *     class="glyphicon glyphicon-remove text-danger"></span>`
     */
    public $falseIcon;

    /**
     * @var bool whether to show null value as a false icon.
     */
    public $showNullAsFalse = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (empty($this->trueLabel)) {
            $this->trueLabel = Yii::t('kvgrid', 'Active');
        }
        if (empty($this->falseLabel)) {
            $this->falseLabel = Yii::t('kvgrid', 'Inactive');
        }
        $this->filter = [true => $this->trueLabel, false => $this->falseLabel];

        if (empty($this->trueIcon)) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->trueIcon = ($this->grid->bootstrap) ? GridView::ICON_ACTIVE : $this->trueLabel;
        }

        if (empty($this->falseIcon)) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->falseIcon = ($this->grid->bootstrap) ? GridView::ICON_INACTIVE : $this->falseLabel;
        }

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function getDataCellValue($model, $key, $index)
    {
        $value = parent::getDataCellValue($model, $key, $index);
        if ($value !== null) {
            return $value ? $this->trueIcon : $this->falseIcon;
        }
        return $this->showNullAsFalse ? $this->falseIcon : $value;
    }
}
