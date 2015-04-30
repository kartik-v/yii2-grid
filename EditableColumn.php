<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2015
 * @version   3.0.2
 */

namespace kartik\grid;

use Yii;
use Closure;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\editable\Editable;

/**
 * The EditableColumn converts the data to editable using
 * the Editable widget [[\kartik\editable\Editable]]
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class EditableColumn extends DataColumn
{

    /**
     * @var array|Closure the configuration options for the [[\kartik\editable\Editable]] widget. If not set as an
     *     array, this can be passed as a callback function of the signature: `function ($model, $key, $index)`, where:
     * - $model mixed is the data model
     * - $key mixed is the key associated with the data model
     * - $index integer is the zero-based index of the data model among the models array returned by
     *     [[GridView::dataProvider]].
     * - $widget EditableColumn is the editable column widget instance
     */
    public $editableOptions = [];

    /**
     * @var boolean whether to refresh the grid on successful submission of editable
     */
    public $refreshGrid = false;

    /**
     * @var boolean|Closure whether to prevent rendering the editable behavior
     * and display a readonly data. You can also set this up as an anonymous function
     * of the form `function($model, $key, $index, $widget)` that will return a boolean
     * value, where:
     * - $model mixed is the data model
     * - $key mixed is the key associated with the data model
     * - $index integer is the zero-based index of the data model among the models array
     *   returned by [[GridView::dataProvider]].
     * - $widget EditableColumn is the editable column widget instance
     */
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
        \kartik\base\Config::checkDependency('editable\Editable', 'yii2-editable', 'for GridView EditableColumn');
        $this->_css = 'kv-edcol-' . hash('crc32', uniqid(rand(1,100), true)); 
    }

    /**
     * Renders the data cell content.
     *
     * @param mixed   $model the data model
     * @param mixed   $key the key associated with the data model
     * @param integer $index the zero-based index of the data model among the models array returned by
     *     [[GridView::dataProvider]].
     *
     * @return string the rendering result
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
        $options = ArrayHelper::getValue($this->_editableOptions, 'containerOptions', []);
        Html::addCssClass($options, $this->_css);
        $this->_editableOptions['containerOptions'] = $options;
        if ($this->grid->pjax && empty($this->_editableOptions['pjaxContainerId'])) {
            $this->_editableOptions['pjaxContainerId'] = $this->grid->pjaxSettings['options']['id'];
        }
        $strKey = $key;
        if (empty($key)) {
            throw new InvalidConfigException("Invalid or no primary key found for the grid data.");
        } elseif (!is_string($key) && !is_numeric($key)) {
            $strKey = serialize($key);
        }
        if ($this->attribute !== null) {
            $this->_editableOptions['model'] = $model;
            $this->_editableOptions['attribute'] = "[{$index}]{$this->attribute}";
            $type = ArrayHelper::getValue($this->_editableOptions, 'inputType', Editable::INPUT_TEXT);
        } elseif (empty($this->_editableOptions['name']) && empty($this->_editableOptions['model']) ||
            !empty($this->_editableOptions['model']) && empty($this->_editableOptions['attribute'])
        ) {
            throw new InvalidConfigException(
                "You must setup the 'attribute' for your EditableColumn OR set one of 'name' OR 'model' & 'attribute' in 'editableOptions' (Exception at index: '{$index}', key: '{$strKey}')."
            );
        }
        $val = $this->getDataCellValue($model, $key, $index);
        if (!isset($this->_editableOptions['displayValue']) && $val !== null && $val !== '') {
            $this->_editableOptions['displayValue'] = parent::renderDataCellContent($model, $key, $index);
        }
        $params = Html::hiddenInput('editableIndex', $index) . Html::hiddenInput('editableKey', $strKey);
        if (empty($this->_editableOptions['beforeInput'])) {
            $this->_editableOptions['beforeInput'] = $params;
        } else {
            $output = $this->_editableOptions['beforeInput'];
            $this->_editableOptions['beforeInput'] = function($form, $widget) use ($output, $params) {
                if ($output instanceof Closure) {
                    return $params . call_user_func($output, $form, $widget);
                } else {
                    return $params . $output;
                }
            };
        }
        if ($this->refreshGrid) {
            $view = $this->grid->getView();
            $grid = 'jQuery("#' . $this->grid->options['id'] . '")';
            $script = <<< JS
{$grid}.find('.{$this->_css}').each(function() {
    var \$el = $(this);
    \$el.on('editableSuccess', function(){
        {$grid}.yiiGridView("applyFilter");
    });
});
JS;
            $view->registerJs($script);
        }
        return Editable::widget($this->_editableOptions);
    }
}