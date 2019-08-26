<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2019
 * @version   3.3.4
 */

namespace kartik\grid;

use Yii;
use Closure;
use yii\base\InvalidConfigException;
use yii\grid\Column;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * RadioColumn displays a column of radio inputs in a grid view. It is different than the CheckboxColumn in the sense
 * that it allows only a single row to be selected at a time.
 *
 * To add a RadioColumn to the gridview, add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         'class' => RadioColumn::className(),
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class RadioColumn extends Column
{
    use ColumnTrait;

    /**
     * @var string the name of the radio input fields.
     */
    public $name = 'kvradio';

    /**
     * @var boolean whether to show the clear button in the header to clear the radio.
     */
    public $showClear = true;

    /**
     * @var array the HTML attributes for the clear button in the header. The following special option is recognized:
     * - label: string, the label for the button (defaults to `&times;`);
     */
    public $clearOptions = ['class' => 'close'];

    /**
     * @var array|\Closure the HTML attributes for radio inputs. This can either be an array of attributes or an
     * anonymous function ([[Closure]]) that returns such an array. The signature of the function should be the
     * following: `function ($model, $key, $index, $column)`. Where `$model`, `$key`, and `$index` refer to the
     * model, key and index of the row currently being rendered and `$column` is a reference to the [[RadioColumn]]
     * object. A function may be used to assign different attributes to different rows based on the data in that
     * row. Specifically if you want to set a different value for the radio, you can use this option in the
     * following way (in this example using the `name` attribute of the model):
     *
     * ```php
     * 'radioOptions' => function($model, $key, $index, $column) {
     *     return ['value' => $model->name];
     * }
     * ```
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $radioOptions = [];

    /**
     * @var boolean highlight current row if checkbox is checked
     */
    public $rowHighlight = true;

    /**
     * @var string highlight CSS class to be applied for highlighting the row. Defaults to 'success'.
     */
    public $rowSelectedClass;

    /**
     * @var string the variables for the client script
     */
    protected $_clientVars = '';

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        $this->initColumnSettings([
            'hiddenFromExport' => true,
            'mergeHeader' => true,
            'hAlign' => GridView::ALIGN_CENTER,
            'vAlign' => GridView::ALIGN_MIDDLE,
            'width' => '50px'
        ]);
        if (empty($this->name)) {
            throw new InvalidConfigException('The "name" property must be set.');
        }
        if (!isset($this->rowSelectedClass)) {
            $this->rowSelectedClass = $this->grid->getCssClass(GridView::BS_TABLE_SUCCESS);
        }
        $css = $this->rowHighlight ? $this->rowSelectedClass : '';
        $this->_view = $this->grid->getView();
        RadioColumnAsset::register($this->_view);
        $grid = $this->grid->options['id'];
        $this->_clientVars = "'{$grid}', '{$this->name}', '{$css}'";
        $this->_clientScript = "kvSelectRadio({$this->_clientVars});";
        $this->_view->registerJs($this->_clientScript);
        $this->parseFormat();
        $this->parseVisibility();
        parent::init();
        $this->setPageRows();
    }

    /**
     * @inheritdoc
     */
    protected function renderHeaderCellContent()
    {
        if ($this->header !== null || !$this->showClear) {
            return parent::renderHeaderCellContent();
        } else {
            $label = ArrayHelper::remove($this->clearOptions, 'label', '&times;');
            Html::addCssClass($this->clearOptions, 'kv-clear-radio');
            if (empty($this->clearOptions['title'])) {
                $this->clearOptions['title'] = Yii::t('kvgrid', 'Clear selection');
            }
            $this->_view->registerJs("kvClearRadio({$this->_clientVars});");
            return Html::button($label, $this->clearOptions);
        }
    }

    /**
     * @inheritdoc
     */
    public function renderDataCell($model, $key, $index)
    {
        $this->initPjax($this->_clientScript);
        $options = $this->fetchContentOptions($model, $key, $index);
        Html::addCssClass($options, 'kv-row-radio-select');
        return Html::tag('td', $this->renderDataCellContent($model, $key, $index), $options);
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->radioOptions instanceof Closure) {
            $options = call_user_func($this->radioOptions, $model, $key, $index, $this);
        } else {
            $options = $this->radioOptions;
            if (!isset($options['value'])) {
                $options['value'] = is_array($key) ? Json::encode($key) : $key;
            }
        }
        return Html::radio($this->name, !empty($options['checked']), $options);
    }
}
