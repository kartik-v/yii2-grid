<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2017
 * @version   3.1.7
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
     * @var boolean whether the column is hidden from display. This is different than the `visible` property, in the
     * sense, that the column is rendered, but hidden from display. This will allow you to still export the column
     * using the export function.
     */
    public $hidden;

    /**
     * @var boolean|array whether the column is hidden in export output. If set to boolean `true`, it will hide the
     * column for all export formats. If set as an array, it will accept the list of GridView export `formats` and
     * hide output only for them.
     */
    public $hiddenFromExport = false;

    /**
     * @var string the horizontal alignment of each column. Should be one of [[GridView::ALIGN_LEFT]],
     * [[GridView::ALIGN_RIGHT]], or [[GridView::ALIGN_CENTER]].
     */
    public $hAlign;

    /**
     * @var string the vertical alignment of each column. Should be one of [[GridView::ALIGN_TOP]],
     * [[GridView::ALIGN_BOTTOM]], or [[GridView::ALIGN_MIDDLE]].
     */
    public $vAlign;

    /**
     * @var boolean whether to force no wrapping on all table cells in the column
     * @see http://www.w3schools.com/cssref/pr_text_white-space.asp
     */
    public $noWrap = false;

    /**
     * @var string the width of each column (matches the CSS width property).
     * @see http://www.w3schools.com/cssref/pr_dim_width.asp
     */
    public $width;

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
     * @var boolean|string|Closure the page summary that is displayed above the footer. You can set it to one of the
     * following:
     * - `false`: the summary will not be displayed.
     * - `true`: the page summary for the column will be calculated and displayed using the
     *   [[pageSummaryFunc]] setting.
     * - `string`: will be displayed as is.
     * - `Closure`: you can set it to an anonymous function with the following signature:
     *
     *   ```php
     *   // example 1
     *   function ($summary, $data, $widget) { return 'Count is ' . $summary; }
     *   // example 2
     *   function ($summary, $data, $widget) { return 'Range ' . min($data) . ' to ' . max($data); }
     *   ```
     *
     *   where:
     *
     *   - the `$summary` variable will be replaced with the calculated summary using the [[pageSummaryFunc]] setting.
     *   - the `$data` variable will contain array of the selected page rows for the column.
     */
    public $pageSummary = false;

    /**
     * @var string|Closure the summary function that will be used to calculate the page summary for the column. If
     * setting as `Closure`, you can set it to an anonymous function with the following signature:
     *
     * ```php
     * function ($data)
     * ```
     *
     *   - the `$data` variable will contain array of the selected page rows for the column.
     */
    public $pageSummaryFunc = GridView::F_SUM;

    /**
     * @var array HTML attributes for the page summary cell. The following special attributes are available:
     * - `prepend`: _string_, a prefix string that will be prepended before the pageSummary content
     * - `append`: _string_, a suffix string that will be appended after the pageSummary content
     */
    public $pageSummaryOptions = [];

    /**
     * @var boolean whether to just hide the page summary display but still calculate the summary based on
     * [[pageSummary]] settings
     */
    public $hidePageSummary = false;

    /**
     * @var boolean whether to merge the header title row and the filter row. This will not render the filter for the
     * column and can be used when `filter` is set to `false`. Defaults to `false`. This is only applicable when
     * [[GridView::filterPosition]] for the grid is set to [[GridView::FILTER_POS_BODY]].
     */
    public $mergeHeader = false;

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
     * @var array collection of row data for the column for the current page
     */
    protected $_rows = [];

    /**
     * @var \yii\web\View the view instance
     */
    protected $_view;

    /**
     * @var string the internally generated client script to initialize
     */
    protected $_clientScript = '';

    /**
     * @var string the internally generated column key
     */
    protected $_columnKey = '';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_view = $this->grid->getView();
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
                $options['pjaxContainerId'] = $this->grid->pjaxSettings['options']['id'];
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
