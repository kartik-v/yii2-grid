<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   3.3.4
 */

namespace kartik\grid;

use Yii;
use yii\helpers\Html;

/**
 * A BooleanColumn will convert true/false values as user friendly indicators with an automated drop down filter for the
 * [[GridView]] widget.
 *
 * To add a BooleanColumn to the gridview, add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         'class' => BooleanColumn::className(),
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class BooleanColumn extends DataColumn
{
    /**
     * @inheritdoc
     */
    public $format = 'raw';

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
     * GridView `bootstrap` property is set to true - it will default to [[GridView::ICON_ACTIVE]].
     */
    public $trueIcon;

    /**
     * @var string icon/indicator for the false value. If this is null, it will use the value from `falseLabel`. If
     * GridView `bootstrap` property is set to true - it will default to [[GridView::ICON_INACTIVE]].
     */
    public $falseIcon;

    /**
     * @var boolean whether to show null value as a false icon.
     */
    public $showNullAsFalse = false;

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        $this->initColumnSettings([
            'hAlign' => GridView::ALIGN_CENTER,
            'width' => '90px'
        ]);
        if (empty($this->trueLabel)) {
            $this->trueLabel = Yii::t('kvgrid', 'Active');
        }
        if (empty($this->falseLabel)) {
            $this->falseLabel = Yii::t('kvgrid', 'Inactive');
        }
        $this->filter = [true => $this->trueLabel, false => $this->falseLabel];
        if (empty($this->trueIcon)) {
            $this->trueIcon = $this->getIconMarkup('true');
        }

        if (empty($this->falseIcon)) {
            $this->falseIcon = $this->getIconMarkup('false');
        }
        parent::init();
    }

    /**
     * Get icon HTML markup
     * @param string $type the type of markup `true` or `false`
     * @return string
     * @throws \yii\base\InvalidConfigException
     */
    protected function getIconMarkup($type = 'true')
    {
        $label = $type === 'false' ? $this->falseLabel: $this->trueLabel;
        if (!$this->grid->bootstrap) {
            return $label;
        }
        $isBs4 = $this->grid->isBs4();
        if ($type === 'true') {
            return ($isBs4 ? GridView::ICON_ACTIVE_BS4 : GridView::ICON_ACTIVE) . 
                Html::tag('span', $this->trueLabel, ['class' => 'kv-grid-boolean']);
        }
        return ($isBs4 ? GridView::ICON_INACTIVE_BS4 : GridView::ICON_INACTIVE) . 
                Html::tag('span', $this->falseLabel, ['class' => 'kv-grid-boolean']);
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
