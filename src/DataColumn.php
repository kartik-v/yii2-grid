<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2020
 * @version   3.3.5
 */

namespace kartik\grid;

use Closure;
use yii\grid\DataColumn as YiiDataColumn;
use kartik\base\Config;
use yii\helpers\Html;

/**
 * The DataColumn is the default column type for the [[GridView]] widget and extends the [[YiiDataColumn]] with various
 * enhancements.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since  1.0
 */
class DataColumn extends YiiDataColumn
{
    use ColumnTrait;

    /**
     * @var string the filter input type for each filter input. You can use one of the `GridView::FILTER_` constants or
     * pass any widget classname (extending the Yii Input Widget).
     */
    public $filterType;

    /**
     * @var array the options/settings for the filter widget. Will be used only if you set `filterType` to a widget
     * classname that exists.
     */
    public $filterWidgetOptions = [];

    /**
     * @var boolean whether to group grid data by this column. Defaults to `false`. Note that your query must sort the
     * data by this column for it to be effective.
     */
    public $group = false;

    /**
     * @var boolean|Closure, whether to add a separate group row for grouping. This is applicable only when `group`
     * property is `true`. Defaults to `false`. If set to `true`, the column will be hidden and its value will be
     * displayed in a separate row above. The default behavior is to show the grouped content in a separate column
     * (when this property is `false`). If setup as a Closure, the signature of the function should be: `function
     * ($model, $key, $index, $column)`, where `$model`, `$key`, and `$index` refer to the model, key and index of
     * the row currently being rendered, and `$column` is a reference to the [[DataColumn]] object.
     */
    public $groupedRow = false;

    /**
     * @var string|Closure, the odd group css class. Defaults to 'kv-group-odd'. If setup as a Closure, the signature
     * of the function should be: `function ($model, $key, $index, $column)`, where `$model`, `$key`, and `$index`
     * refer to the model, key and index of the row currently being rendered, and `$column` is a reference to the
     * [[DataColumn]] object.
     */
    public $groupOddCssClass = 'kv-group-odd';

    /**
     * @var string|Closure, the even group css class. Defaults to 'kv-group-even'. If setup as a Closure, the signature
     * of the function should be: `function ($model, $key, $index, $column)`, where `$model`, `$key`, and `$index`
     * refer to the model, key and index of the row currently being rendered, and `$column` is a reference to the
     * [[DataColumn]] object.
     */
    public $groupEvenCssClass = 'kv-group-even';

    /**
     * @var integer|Closure the column index of which this group is a sub group of. This is validated only if `group`
     * is set to `true`.  If setup as a Closure, the signature of the function should be: `function ($model, $key,
     * $index, $column)`, where `$model`, `$key`, and `$index` refer to the model, key and index of the row
     * currently being rendered, and `$column` is a reference to the [[DataColumn]] object.
     */
    public $subGroupOf;

    /**
     * @var array|Closure configuration of the group header which will be displayed as a separate row above the group.
     * If this is empty, no group header will be rendered. If setup as a Closure, the signature of the function
     * should be: `function ($model, $key, $index, $column)`, where `$model`, `$key`, and `$index` refer to the
     * model, key and index of the row currently being rendered, and `$column` is a reference to the [[DataColumn]]
     * object. The following array keys are recognized:
     *
     * - `mergeColumns`: `array`, of columns that will be merged as `from, to` pairs. For example if you need to merge
     *   column numbers 0 to 2 and column numbers 3 to 6, you can set this as:
     *   ```php
     *   [
     *     [0, 2], [3, 6]
     *   ]
     *   ```
     * - `content`: `array`, header content for each column. You must set this as `$key => $value`, where `$key` is the
     * zero based index for the column, and `$value` is the content to display for the column. The `$value` can take in
     * special function names to summarize values for the column. If set to one of [[GridView::F_COUNT]],
     * [[GridView::F_SUM]], [[GridView::F_AVG]], [[GridView::F_MAX]], [[GridView::F_MIN]], the values will be auto summarized.
     * - `contentFormats`: `array`, header content formats for each column. This is only applicable currently only for
     * number type or a custom type using a javascript callback. You must set this as `$key => $value`, where
     * `$key` is the 0 based index for the column, and  `$value` is the format settings for the column. The
     * `$value` is a format specification setup as an array containing one or more of the following options:
     *    - `format`: string, whether `number` or `callback`
     *    - `decimals`: number, number of decimals (for number format only)
     *    - `decPoint`: string, decimals point character (for number format only). Defaults to `.`.
     *    - `thousandSep`: string, thousands separator character (for number format only). Defaults to `,`.
     *    - `func`: string, the javascript callback function name (for callback format only). This should be set to a
     *      globally accessible javascript function name. For example if you set this to `customCallback`, the function
     *      should be of the signature: `function customCallback(source, data) { return custom_convert(source, data);}`.
     *      The parameters for the callback function that will be passed are:
     *    - `source`: string, the summary column source as set in `content` section if available
     *    - `data`: `array`, the text values of each of the child columns in this group.
     *  An example of setting the `content`:
     *    ```php
     *    [
     *       7 => ['format'=>'callback', 'func'=>'customCallback']
     *       8 => ['format'=>'number', 'decimals'=>2, 'decPoint'=>'.', 'thousandSep'=>',']
     *    ]
     *    ```
     * - `contentOptions`: `array`, header HTML attributes for each column. You must set this as `$key => $value`, where
     * `$key` is the 0 based index for the column, and `$value` is the HTML attributes to apply for the column. The
     * `$value` must be an array of HTML attributes for the table column. An example of setting the `contentOptions`:
     *  ```php
     *  [
     *      0 => ['style'=>'font-weight:bold'],
     *      8 => ['style'=>'text-align:right']
     *  ]
     *  ```
     * - `options`: `array`, HTML attributes for the group header row.
     */
    public $groupHeader = [];

    /**
     * @var array|Closure configuration of the group footer which will be displayed as a separate row. If this is
     * empty, no group footer will be rendered. If setup as a Closure, the signature of the function should be:
     * `function ($model, $key, $index, $column)`, where `$model`, `$key`, and `$index` refer to the model, key and
     * index of the row currently being rendered, and `$column` is a reference to the [[DataColumn]] object.
     * `$column` is a reference to the [[DataColumn]] object. The following array keys are recognized:
     * - `mergeColumns`: `array`, of columns that will be merged as `from, to` pairs. For example if you need to merge
     *   column numbers 0 to 2 and column numbers 3 to 6, you can set this as:
     *   ```php
     *   [
     *     [0, 2], [3, 6]
     *   ]
     *   ```
     * - `content`: `array`, footer content for each column. You must set this as `$key => $value`, where `$key` is the 0
     *   based index for the column, and `$value` is the content to display for the column. The `$value` can take in
     *   special function names to summarize values for the column. If set to one of `GridView::F_COUNT`,
     *   `GridView::F_SUM`, `GridView::F_AVG`, `GridView::F_MAX`, `GridView::F_MIN`, the values will be auto
     *   summarized. For example:
     *   ```php
     *   [
     *      0 => 'Total',
     *      8 => GridView::F_SUM
     *   ]
     *   ```
     * - `contentFormats`: `array`, footer content formats for each column. This is only applicable currently only for
     *   number type or a custom type using a javascript callback. You must set this as `$key => $value`, where
     *   `$key` is the 0 based index for the column, and  `$value` is the format settings for the column. The
     *   `$value` is a format specification setup as an array containing one or more of the following options:
     *    - `format`: string, whether `number` or `callback`
     *    - `decimals`: number, number of decimals (for number format only)
     *    - `decPoint`: string, decimals point character (for number format only). Defaults to `.`.
     *    - `thousandSep`: string, thousands separator character (for number format only). Defaults to `,`.
     *    - `func`: string, the javascript callback function name (for callback format only). This should be set to a
     *  globally accessible javascript function name. For example if you set this to `customCallback`, the function
     *  should be of the signature: `function customCallback(source, data) { return custom_convert(source, data); }`.
     *  The parameters for the callback function that will be passed are:
     *    - `source`: string, the summary column source as set in `content` section if available
     *    - `data`: `array`, the text values of each of the child columns in this group.
     *  An example of setting the `content`:
     *    ```php
     *    [
     *       7 => ['format'=>'callback', 'func'=>'customCallback']
     *       8 => ['format'=>'number', 'decimals'=>2, 'decPoint'=>'.', 'thousandSep'=>',']
     *    ]
     *    ```
     * - `contentOptions`: `array`, footer HTML attributes for each column. You must set this as `$key => $value`, where
     *   `$key` is the 0 based index for the column, and `$value` is the HTML attributes to apply for the column. The
     *   `$value` must be an array of HTML attributes for the table column. An example of setting the `contentOptions`:
     *    ```php
     *    [
     *       0 => ['style'=>'font-weight:bold'],
     *       8 => ['style'=>'text-align:right']
     *    ]
     *    ```
     * - `options`: `array`, HTML attributes for the group footer row.
     */
    public $groupFooter = [];

    /**
     * @var string the cell format for EXCEL exported content.
     * @see http://cosicimiento.blogspot.in/2008/11/styling-excel-cells-with-mso-number.html
     */
    public $xlFormat;

    /**
     * @var array|Closure configuration for the `\kartik\export\ExportMenu` column cell style that will be utilized by
     * `\PhpOffice\PhpSpreadsheet\Style\Style::applyFromArray()`. This is applicable when configuring this column
     * in `\kartik\export\ExportMenu`. If setup as a Closure, the signature of the function should be: `function
     * ($model, $key, $index, $column)`, where `$model`, `$key`, and `$index` refer to the model, key and index of
     * the row currently being rendered, and `$column` is a reference to the [[DataColumn]] object.
     */
    public $exportMenuStyle = ['alignment' => ['vertical' => GridView::ALIGN_CENTER]];

    /**
     * @var array configuration for the `\kartik\export\ExportMenu` column header cell style that will be utilized by
     * `\PhpOffice\PhpSpreadsheet\Style\Style::applyFromArray()`. This is applicable when configuring this column
     * in `\kartik\export\ExportMenu`.
     */
    public $exportMenuHeaderStyle = ['alignment' => ['vertical' => GridView::ALIGN_CENTER]];

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        $this->initColumnSettings();
        if ($this->mergeHeader && !isset($this->vAlign)) {
            $this->vAlign = GridView::ALIGN_MIDDLE;
        }
        if ($this->grid->bootstrap === false) {
            Html::removeCssClass($this->filterInputOptions, 'form-control');
        }
        $this->parseFormat();
        $this->parseVisibility();
        $this->checkValidFilters();
        parent::init();
        $this->setPageRows();
        $this->initGrouping();
    }

    /**
     * @inheritdoc
     */
    public function renderDataCell($model, $key, $index)
    {
        $options = $this->fetchContentOptions($model, $key, $index);
        $this->parseGrouping($options, $model, $key, $index);
        $this->parseExcelFormats($options, $model, $key, $index);
        $this->initPjax($this->_clientScript);
        return Html::tag('td', $this->renderDataCellContent($model, $key, $index), $options);
    }

    /**
     * Renders filter inputs based on the `filterType`
     *
     * @return string
     * @throws \Exception
     */
    protected function renderFilterCellContent()
    {
        $content = parent::renderFilterCellContent();
        $chkType = !empty($this->filterType) && $this->filterType !== GridView::FILTER_CHECKBOX &&
            $this->filterType !== GridView::FILTER_RADIO && !class_exists($this->filterType);
        if ($this->filter === false || empty($this->filterType) || $content === $this->grid->emptyCell || $chkType) {
            return $content;
        }
        $widgetClass = $this->filterType;
        $options = [
            'model' => $this->grid->filterModel,
            'attribute' => $this->attribute,
            'options' => $this->filterInputOptions,
        ];
        if (is_array($this->filter)) {
            if (Config::isInputWidget($this->filterType) && $this->grid->pjax) {
                $options['pjaxContainerId'] = $this->grid->getPjaxContainerId();
            }
            if ($this->filterType === GridView::FILTER_SELECT2 || $this->filterType === GridView::FILTER_TYPEAHEAD) {
                $options['data'] = $this->filter;
            }
            if ($this->filterType === GridView::FILTER_RADIO) {
                return Html::activeRadioList(
                    $this->grid->filterModel,
                    $this->attribute,
                    $this->filter,
                    $this->filterInputOptions
                );
            }
        }
        if ($this->filterType === GridView::FILTER_CHECKBOX) {
            return Html::activeCheckbox($this->grid->filterModel, $this->attribute, $this->filterInputOptions);
        }
        $options = array_replace_recursive($this->filterWidgetOptions, $options);
        /** @var \kartik\base\Widget $widgetClass */
        return $widgetClass::widget($options);
    }
}
