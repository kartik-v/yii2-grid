<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2021
 * @version   3.3.6
 */

namespace kartik\grid;

use Closure;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use kartik\editable\Editable;
use kartik\base\Config;

/**
 * The EditableColumn converts the data to editable using the Editable widget [[\kartik\editable\Editable]].
 *
 * To add an EditableColumn to the gridview, add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         'class' => EditableColumn::className(),
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class EditableColumn extends DataColumn
{
    /**
     * @var array|Closure the configuration options for the [[\kartik\editable\Editable]] widget. If not set as an
     * array, this can be passed as a callback function of the signature: `function ($model, $key, $index)`, where:
     * - `$model`: _\yii\base\Model_, is the data model.
     * - `$key`: _string|object_, is the primary key value associated with the data model.
     * - `$index`: _integer_, is the zero-based index of the data model among the model array returned by [[dataProvider]].
     * - `$column`: _EditableColumn_, is the column object instance.
     *
     * This property allows to configure these additional settings for configuring the widget options:
     * - `class`: _string_, the Editable widget class name. If not set this defaults to `kartik\editable\Editable`.
     */
    public $editableOptions = [];

    /**
     * @var boolean whether to refresh the grid on successful submission of editable
     */
    public $refreshGrid = false;

    /**
     * @var boolean|Closure whether to prevent rendering the editable behavior and display a readonly data. You can
     * also set this up as an anonymous function of the form `function($model, $key, $index, $widget)` that will return
     * a boolean value, where:
     * - `$model`: _\yii\base\Model_, is the data model.
     * - `$key`: _string|object_, is the primary key value associated with the data model.
     * - `$index`: _integer_, is the zero-based index of the data model among the model array returned by [[dataProvider]].
     * - `$column`: _EditableColumn_, is the column object instance.     */
    public $readonly = false;

    /**
     * @var array the computed editable options
     */
    protected $_editableOptions = [];

    /**
     * @var string the css class to be appended for the editable inputs in this column
     */
    protected $_css;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        $this->_css = 'kv-edcol-' . hash('crc32', uniqid(rand(1, 100), true));
        if ($this->refreshGrid) {
            EditableColumnAsset::register($this->_view);
        }
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function renderDataCellContent($model, $key, $index)
    {
        $readonly = $this->readonly;
        if ($readonly instanceof Closure) {
            $readonly = call_user_func($readonly, $model, $key, $index, $this);
        }
        if ($readonly === true) {
            return parent::renderDataCellContent($model, $key, $index);
        }
        $this->_editableOptions = $this->editableOptions;
        if (!empty($this->editableOptions) && $this->editableOptions instanceof Closure) {
            $this->_editableOptions = call_user_func($this->editableOptions, $model, $key, $index, $this);
        }
        if (!is_array($this->_editableOptions)) {
            $this->_editableOptions = [];
        }
        if (empty($this->_editableOptions['class'])) {
            Config::checkDependency('editable\Editable', 'yii2-editable', 'for GridView EditableColumn');
        } elseif (!class_exists($this->_editableOptions['class'])) {
            throw new InvalidConfigException(
                "The widget class '" . $this->_editableOptions['class'] . "' set in `editableOptions` does not exist."
            );
        }
        $options = ArrayHelper::getValue($this->_editableOptions, 'containerOptions', []);
        Html::addCssClass($options, $this->_css);
        $this->_editableOptions['containerOptions'] = $options;
        if ($this->grid->pjax && empty($this->_editableOptions['pjaxContainerId'])) {
            $this->_editableOptions['pjaxContainerId'] = $this->grid->getPjaxContainerId();
        }
        if (!isset($key)) {
            throw new InvalidConfigException('Invalid or no primary key found for the grid data.');
        }
        $strKey = !is_string($key) && !is_numeric($key) ? (is_array($key) ? Json::encode($key) : (string) $key) : $key;
        if ($this->attribute !== null) {
            $this->_editableOptions['model'] = $model;
            $this->_editableOptions['attribute'] = "[{$index}]{$this->attribute}";
        } elseif (empty($this->_editableOptions['name']) && empty($this->_editableOptions['model']) ||
            !empty($this->_editableOptions['model']) && empty($this->_editableOptions['attribute'])
        ) {
            throw new InvalidConfigException(
                "You must setup the 'attribute' for your EditableColumn OR set one of 'name' OR 'model' & 'attribute'" .
                " in 'editableOptions' (Exception at index: '{$index}', key: '{$strKey}')."
            );
        }
        $val = $this->getDataCellValue($model, $key, $index);
        if (!isset($this->_editableOptions['displayValue']) && $val !== null && $val !== '') {
            $this->_editableOptions['displayValue'] = parent::renderDataCellContent($model, $key, $index);
        }
        $params = Html::hiddenInput('editableIndex', $index) . Html::hiddenInput('editableKey', $strKey) .
            Html::hiddenInput('editableAttribute', $this->attribute);
        if (empty($this->_editableOptions['beforeInput'])) {
            $this->_editableOptions['beforeInput'] = $params;
        } else {
            $output = $this->_editableOptions['beforeInput'];
            $this->_editableOptions['beforeInput'] = function ($form, $widget) use ($output, $params) {
                return $params . ($output instanceof Closure ? call_user_func($output, $form, $widget) : $output);
            };
        }
        if ($this->refreshGrid) {
            $id = $this->grid->options['id'];
            $this->_view->registerJs("kvRefreshEC('{$id}','{$this->_css}');");
        }
        $editableClass = ArrayHelper::remove($this->_editableOptions, 'class', Editable::class);
        if (!isset($this->_editableOptions['inlineSettings']['options'])) {
            $this->_editableOptions['inlineSettings']['options']['class'] = 'skip-export';
        } else {
            Html::addCssClass($this->_editableOptions['inlineSettings']['options'], 'skip-export');
        }
        return $editableClass::widget($this->_editableOptions);
    }
}
