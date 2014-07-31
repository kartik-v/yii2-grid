<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @package yii2-grid
 * @version 1.8.0
 */

namespace kartik\grid;

use Yii;
use yii\helpers\Html;
use yii\base\InvalidConfigException;

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
     * @var array the configuration options for the [[\kartik\editable\Editable]] widget
     */
    public $editableOptions = [];
    
    /**
     * Renders the data cell content.
     * @param mixed $model the data model
     * @param mixed $key the key associated with the data model
     * @param integer $index the zero-based index of the data model among the models array returned by [[GridView::dataProvider]].
     *
     * @return string the rendering result
     * @throws InvalidConfigException
     */
    public function renderDataCellContent($model, $key, $index)
    {
        $exception = false;
        if ($this->attribute !== null) {
            $this->editableOptions['model'] = $model;
            $this->editableOptions['attribute'] = '[' . $index . ']' . $this->attribute;
        } elseif (empty($this->editableOptions['name']) && empty($this->editableOptions['model'])) {
            $exception = true;
        } elseif (empty($this->editableOptions['attribute'])) {
            $exception = true;
        }
        if ($exception) {
            throw new InvalidConfigException("You must setup either the 'attribute' for the EditableColumn OR setup the 'name' OR 'model'/'attribute' in 'editableOptions' (Exception raised for: key: '{$key}', index: '{$index}').");
        }
        $this->editableOptions['displayValue'] = parent::renderDataCellContent($model, $key, $index);
        $params = Html::hiddenInput('editableIndex', $index) . Html::hiddenInput('editableKey', $key);
        if (empty($this->editableOptions['beforeInput'])) {
            $this->editableOptions['beforeInput'] = $params;
        } else {
            $this->editableOptions['beforeInput'] .= $params;
        }
        return \kartik\editable\Editable::widget($this->editableOptions);
    }
}