<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2022
 * @version   3.5.0
 */

namespace kartik\grid;

use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JsExpression;

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
 *         'class' => BooleanColumn::class,
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
    public $width = '110px';
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
     * @var bool whether to use Krajee Select2 widget as the filter
     */
    public $useSelect2Filter = false;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        $this->initColumnSettings([
            'hAlign' => GridView::ALIGN_CENTER,
            'width' => '90px',
        ]);
        if (empty($this->trueLabel)) {
            $this->trueLabel = Yii::t('kvgrid', 'Active');
        }
        if (empty($this->falseLabel)) {
            $this->falseLabel = Yii::t('kvgrid', 'Inactive');
        }
        $this->filter = [true => $this->trueLabel, false => $this->falseLabel];
        if (empty($this->trueIcon)) {
            $this->trueIcon = $this->getIconMarkup();
        }

        if (empty($this->falseIcon)) {
            $this->falseIcon = $this->getIconMarkup('false');
        }
        $this->initColumnFilter();
        parent::init();
    }

    /**
     * Initialize column filter
     */
    protected function initColumnFilter()
    {
        $placeholder = Yii::t('kvgrid', 'Select...');
        if (empty($this->useSelect2Filter)) {
            if ($this->grid->bootstrap && $this->grid->isBs(5)) {
                Html::removeCssClass($this->filterInputOptions, 'form-control');
                Html::addCssClass($this->filterInputOptions, 'form-select');
            }
            if (!isset($this->filterInputOptions['prompt'])) {
                $this->filterInputOptions['prompt'] = $placeholder;
            }
            return;
        }
        $this->filterType = GridView::FILTER_SELECT2;
        $config = Json::encode([$this->getIconLabel('false'), $this->getIconLabel('true')]);
        $format = <<< JS
function(status) {
    var cfg={$config}, out = cfg[status.id];
    return !out || !out[0] ? status.text : '<span class="kv-bool-icon">' + out[0] + '</span> ' + out[1];
}
JS;
        $format = new JsExpression($format);
        $opts = [
            'pluginOptions' => [
                'templateResult' => $format,
                'templateSelection' => $format,
                'escapeMarkup' => new JsExpression('function(m){return m}'),
                'allowClear' => true,
            ],
            'options' => ['placeholder' => $placeholder]
        ];
        $this->filterWidgetOptions = array_replace_recursive($opts, $this->filterWidgetOptions);
    }

    /**
     * Gets the icon and label
     * @param  string  $type
     * @return array
     * @throws Exception
     */
    protected function getIconLabel($type = 'true') {
        $isTrue = $type === 'true';
        $label = $isTrue ? $this->trueLabel : $this->falseLabel;
        $notBs3 = !$this->grid->isBs(3);
        $icon = $notBs3 ? GridView::ICON_INACTIVE_BS4 : GridView::ICON_INACTIVE;
        if ($isTrue) {
            $icon = $notBs3 ? GridView::ICON_ACTIVE_BS4 : GridView::ICON_ACTIVE;
        }
        return [$icon, $label];
    }

    /**
     * Get icon HTML markup
     * @param  string  $type  the type of markup `true` or `false`
     * @return string
     * @throws InvalidConfigException|Exception
     */
    protected function getIconMarkup($type = 'true')
    {
        $label = $type === 'true' ? $this->trueLabel : $this->falseLabel;
        if (!$this->grid->bootstrap) {
            return $label;
        }
        $cfg = $this->getIconLabel($type);

        return Html::tag('span', $cfg[0], ['class' => 'skip-export']).
            Html::tag('span', $cfg[1], ['class' => 'kv-grid-boolean']);
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