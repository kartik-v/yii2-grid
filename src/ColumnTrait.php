<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2023
 * @version   3.5.3
 */

namespace kartik\grid;

use Closure;
use kartik\base\Config;
use kartik\base\Lib;
use yii\base\InvalidConfigException;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * ColumnTrait maintains generic methods used by all column widgets in [[GridView]].
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
trait ColumnTrait
{
    /**
     * @var string unique identifier for the Column. If not set, it will be automatically generated.
     */
    public $columnKey;

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
    public $hiddenFromExport;

    /**
     * @var boolean whether to strip HTML tags from the column during export. This can be useful for column data that
     * is mainly a hyperlink or contains HTML markup that are not needed for display at export. Defaults to `false`.
     */
    public $stripTagsFromExport = false;

    /**
     * @var boolean whether to force no wrapping on all table cells in the column
     * @see http://www.w3schools.com/cssref/pr_text_white-space.asp
     */
    public $noWrap;

    /**
     * @var boolean whether to merge the header title row and the filter row. This will not render the filter for the
     * column and can be used when `filter` is set to `false`. Defaults to `false`. This is only applicable when
     * [[GridView::filterPosition]] for the grid is set to [[GridView::FILTER_POS_BODY]].
     */
    public $mergeHeader;

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
    public $pageSummary;

    /**
     * @var boolean whether to just hide the page summary display but still calculate the summary based on
     * [[pageSummary]] settings
     */
    public $hidePageSummary;

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
    public $pageSummaryFunc;

    /**
     * @var array HTML attributes for the page summary cell. The following special attributes are available:
     * - `prepend`: _string_, a prefix string that will be prepended before the pageSummary content
     * - `append`: _string_, a suffix string that will be appended after the pageSummary content
     * - `colspan`: _int_, the column count that will be merged.
     * - `data-colspan-dir`: _string_, whether `ltr` or `rtl`. Defaults to `ltr`. If this is set to `ltr` the columns
     *    will be merged starting from this column to the right (i.e. left to right). If this is set to `rtl`, the columns
     *    will be merged starting from this column to the left (i.e. right to left).
     */
    public $pageSummaryOptions;

    /**
     * @var string|array|Closure in which format should the value of each data model be displayed as (e.g. `"raw"`, `"text"`, `"html"`,
     * `['date', 'php:Y-m-d']`). Supported formats are determined by the [[GridView::formatter|formatter]] used by
     * the [[GridView]]. Default format is "text" which will format the value as an HTML-encoded plain text when
     * [[\yii\i18n\Formatter]] is used as the [[GridView::$formatter|formatter]] of the GridView.
     *
     * If this is not set - it will default to the `format` setting for the Column.
     *
     * @see \yii\i18n\Formatter::format()
     */
    public $pageSummaryFormat;

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
     * @var string the width of each column (matches the CSS width property).
     * @see http://www.w3schools.com/cssref/pr_dim_width.asp
     */
    public $width;

    /**
     * @var array collection of row data for the column for the current page
     */
    protected $_rows = [];

    /**
     * @var View the view instance
     */
    protected $_view;

    /**
     * @var string the internally generated client script to initialize
     */
    protected $_clientScript = '';

    /**
     * Initialize column settings
     * @param array $settings
     */
    public function initColumnSettings($settings = [])
    {
        $this->_view = $this->grid->getView();
        $settings += [
            'hidden' => false,
            'hiddenFromExport' => false,
            'noWrap' => false,
            'mergeHeader' => false,
            'pageSummary' => false,
            'hidePageSummary' => false,
            'pageSummaryFunc' => GridView::F_SUM,
            'pageSummaryOptions' => [],
        ];
        foreach ($settings as $key => $val) {
            if (!isset($this->$key)) {
                $this->$key = $val;
            }
        }
        $this->initColumnKey();

        if (!empty($this->filter) && empty($this->filterType) && $this->grid->isBs(5)) {
            Html::addCssClass($this->filterInputOptions, 'form-select');
        }
    }

    /**
     * Renders the header cell.
     *
     * @return string
     */
    public function renderHeaderCell()
    {
        if ($this->grid->filterModel !== null && $this->mergeHeader && $this->grid->filterPosition === GridView::FILTER_POS_BODY) {
            $this->headerOptions['rowspan'] = 2;
            Html::addCssClass($this->headerOptions, 'kv-merged-header');
        }
        $this->headerOptions['data-col-seq'] = array_search($this, $this->grid->columns);
        return parent::renderHeaderCell();
    }

    /**
     * Renders the filter cell.
     *
     * @return string
     */
    public function renderFilterCell()
    {
        if ($this->grid->filterModel !== null && $this->mergeHeader && $this->grid->filterPosition === GridView::FILTER_POS_BODY) {
            return null;
        }
        $this->filterOptions['data-col-seq'] = array_search($this, $this->grid->columns);
        return parent::renderFilterCell();
    }

    /**
     * Renders the page summary cell.
     *
     * @return string the rendered result
     */
    public function renderPageSummaryCell()
    {
        $prepend = ArrayHelper::remove($this->pageSummaryOptions, 'prepend', '');
        $append = ArrayHelper::remove($this->pageSummaryOptions, 'append', '');
        return Html::tag('td', $prepend . $this->renderPageSummaryCellContent() . $append, $this->pageSummaryOptions);
    }

    /**
     * Parses Excel Cell Formats for export
     *
     * @param array $options the HTML attributes for the cell
     * @param Model $model the current model being rendered
     * @param mixed $key the primary key value for the model
     * @param integer $index the zero-based index of the model being rendered
     */
    public function parseExcelFormats(&$options, $model, $key, $index)
    {
        $autoFormat = $this->grid->autoXlFormat;
        if (!isset($this->xlFormat) && !$autoFormat) {
            return;
        }
        $fmt = '';
        $format = is_array($this->format) ? $this->format[0] : $this->format;
        $formatter = $this->grid->formatter;
        if (isset($this->xlFormat)) {
            $fmt = $this->xlFormat;
        } elseif ($autoFormat && isset($this->format)) {
            $tSep = isset($formatter->thousandSeparator) ? $formatter->thousandSeparator : ',';
            $dSep = isset($formatter->decimalSeparator) ? $formatter->decimalSeparator : '.';
            switch ($format) {
                case 'text':
                case 'html':
                case 'raw':
                case 'ntext':
                case 'paragraphs':
                case 'spellout':
                case 'bool':
                case 'relativeTime':
                    $fmt = '\@';
                    break;
                case 'integer':
                    $fmt = "\\#\\{$tSep}\\#\\#0";
                    break;
                case 'decimal':
                case 'percent':
                case 'scientific':
                    $decimals = is_array($this->format) && isset($this->format[1]) ? $this->format[1] : 2;
                    $append = $decimals > 0 ? "\\{$dSep}" . Lib::str_repeat('0', $decimals) : '';
                    if ($format == 'percent') {
                        $append .= '%';
                    }
                    $fmt = ($format == 'scientific') ? "0{$append}E+00" : "\\#\\{$tSep}\\#\\#0" . $append;
                    break;
                case 'currency':
                    $curr = is_array($this->format) && isset($this->format[1]) ? $this->format[1] :
                        (isset($formatter->currencyCode) ? $formatter->currencyCode . ' ' : '');
                    $fmt = "{$curr}\\#\\{$tSep}\\#\\#0{$dSep}00";
                    break;
                case 'date':
                case 'time':
                    $fmt = 'Short ' . Lib::ucfirst($format);
                    break;
                case 'datetime':
                    $fmt = 'yyyy\-MM\-dd HH\:mm\:ss';
                    break;
                default:
                    $fmt = '';
                    break;
            }
        }
        if ($format === 'date' || $format === 'datetime' || $format === 'time') {
            $rawValue = $this->getDataCellValue($model, $key, $index);
            switch ($format) {
                case 'date':
                    $rawValue = $formatter->format($rawValue, ['date', 'php:Y-m-d']);
                    break;
                case 'datetime':
                    $rawValue = $formatter->format($rawValue, ['date', 'php:Y-m-d H:i:s']);
                    break;
                case 'time':
                    $rawValue = $formatter->format($rawValue, ['date', 'php:H:i:s']);
                    break;
            }
            $options['data-raw-value'] = $rawValue;
        } elseif ($format === 'integer' || $format === 'decimal' || $format === 'percent' || $format === 'scientific') {
            $options['data-raw-value'] = $this->getDataCellValue($model, $key, $index);
        }
        Html::addCssStyle($options, ['mso-number-format' => $fmt]);
    }

    /**
     * Renders the page summary cell content.
     *
     * @return string the rendered result
     */
    protected function renderPageSummaryCellContent()
    {
        if ($this->hidePageSummary) {
            return $this->grid->emptyCell;
        }
        $content = $this->getPageSummaryCellContent();
        if ($this->pageSummary === true) {
            $format = isset($this->pageSummaryFormat) ? $this->pageSummaryFormat : $this->format;
            return $this->grid->formatter->format($content, $format);
        }
        return ($content === null) ? $this->grid->emptyCell : $content;
    }

    /**
     * Gets the raw page summary cell content.
     *
     * @return string the rendered result
     */
    protected function getPageSummaryCellContent()
    {
        if ($this->pageSummary === true || $this->pageSummary instanceof Closure) {
            $summary = $this->calculateSummary();
            return ($this->pageSummary === true) ? $summary : call_user_func(
                $this->pageSummary,
                $summary,
                $this->_rows,
                $this
            );
        }
        if ($this->pageSummary !== false) {
            return $this->pageSummary;
        }
        return null;
    }

    /**
     * Calculates the summary of an input data based on page summary aggregration function.
     *
     * @return float
     */
    protected function calculateSummary()
    {
        $type = $this->pageSummaryFunc;
        if ($type instanceof Closure) {
            return call_user_func($type, $this->_rows);
        }
        if (empty($this->_rows)) {
            return '';
        }
        $data = $this->_rows;
        switch ($type) {
            case null:
            case GridView::F_SUM:
                return array_sum($data);
            case GridView::F_COUNT:
                return count($data);
            case GridView::F_AVG:
                return count($data) > 0 ? array_sum($data) / count($data) : null;
            case GridView::F_MAX:
                return max($data);
            case GridView::F_MIN:
                return min($data);
        }
        return '';
    }

    /**
     * Checks if the filter input types are valid
     * @throws InvalidConfigException
     */
    protected function checkValidFilters()
    {
        if (isset($this->filterType)) {
            Config::validateInputWidget($this->filterType, 'for filtering the grid as per your setup');
        }
    }

    /**
     * Checks `hidden` property and hides the column from display
     */
    protected function parseVisibility()
    {
        if ($this->stripTagsFromExport) {
            Html::addCssClass($this->options, 'strip-tags-export');
        }
        if ($this->hidden === true) {
            Html::addCssClass($this->filterOptions, 'kv-grid-hide');
            Html::addCssClass($this->headerOptions, 'kv-grid-hide');
            Html::addCssClass($this->footerOptions, 'kv-grid-hide');
            Html::addCssClass($this->pageSummaryOptions, 'kv-grid-hide');
        }
        if ($this->hiddenFromExport === true) {
            Html::addCssClass($this->filterOptions, 'skip-export');
            Html::addCssClass($this->headerOptions, 'skip-export');
            Html::addCssClass($this->footerOptions, 'skip-export');
            Html::addCssClass($this->pageSummaryOptions, 'skip-export');
            Html::addCssClass($this->options, 'skip-export');
        }
        if (is_array($this->hiddenFromExport) && !empty($this->hiddenFromExport)) {
            $tag = 'skip-export-';
            $css = $tag . implode(" {$tag}", $this->hiddenFromExport);
            Html::addCssClass($this->filterOptions, $css);
            Html::addCssClass($this->headerOptions, $css);
            Html::addCssClass($this->footerOptions, $css);
            Html::addCssClass($this->pageSummaryOptions, $css);
            Html::addCssClass($this->options, $css);
        }
    }

    /**
     * Parses and formats a grid column
     */
    protected function parseFormat()
    {
        if ($this->isValidAlignment()) {
            $class = "kv-align-{$this->hAlign}";
            Html::addCssClass($this->headerOptions, $class);
            Html::addCssClass($this->pageSummaryOptions, $class);
            Html::addCssClass($this->footerOptions, $class);
        }
        if ($this->noWrap) {
            Html::addCssClass($this->headerOptions, GridView::NOWRAP);
            Html::addCssClass($this->pageSummaryOptions, GridView::NOWRAP);
            Html::addCssClass($this->footerOptions, GridView::NOWRAP);
        }
        if ($this->isValidAlignment('vAlign')) {
            $class = "kv-align-{$this->vAlign}";
            Html::addCssClass($this->headerOptions, $class);
            Html::addCssClass($this->pageSummaryOptions, $class);
            Html::addCssClass($this->footerOptions, $class);
        }
        if (Lib::trim($this->width) != '') {
            Html::addCssStyle($this->headerOptions, "width:{$this->width};");
            Html::addCssStyle($this->pageSummaryOptions, "width:{$this->width};");
            Html::addCssStyle($this->footerOptions, "width:{$this->width};");
        }
    }

    /**
     * Check if the alignment provided is valid
     *
     * @param string $type the alignment type
     *
     * @return boolean
     */
    protected function isValidAlignment($type = 'hAlign')
    {
        if ($type === 'hAlign') {
            return (
                $this->hAlign === GridView::ALIGN_LEFT ||
                $this->hAlign === GridView::ALIGN_RIGHT ||
                $this->hAlign === GridView::ALIGN_CENTER
            );
        } elseif ($type === 'vAlign') {
            return (
                $this->vAlign === GridView::ALIGN_TOP ||
                $this->vAlign === GridView::ALIGN_MIDDLE ||
                $this->vAlign === GridView::ALIGN_BOTTOM
            );
        }
        return false;
    }

    /**
     * Parses and fetches updated content options for grid visibility and format
     *
     * @param mixed $model the data model being rendered
     * @param mixed $key the key associated with the data model
     * @param integer $index the zero-based index of the data item among the item array returned by
     * [[GridView::dataProvider]].
     *
     * @return array
     */
    protected function fetchContentOptions($model, $key, $index)
    {
        if ($this->contentOptions instanceof Closure) {
            $options = call_user_func($this->contentOptions, $model, $key, $index, $this);
        } else {
            $options = $this->contentOptions;
        }
        if ($this->hidden === true) {
            Html::addCssClass($options, 'kv-grid-hide');
        }
        if ($this->hiddenFromExport === true) {
            Html::addCssClass($options, 'skip-export');
        }
        if (is_array($this->hiddenFromExport) && !empty($this->hiddenFromExport)) {
            $tag = 'skip-export-';
            $css = $tag . implode(" {$tag}", $this->hiddenFromExport);
            Html::addCssClass($options, $css);
        }
        if ($this->isValidAlignment()) {
            Html::addCssClass($options, "kv-align-{$this->hAlign}");
        }
        if ($this->noWrap) {
            Html::addCssClass($options, GridView::NOWRAP);
        }
        if ($this->isValidAlignment('vAlign')) {
            Html::addCssClass($options, "kv-align-{$this->vAlign}");
        }
        if (Lib::trim($this->width) != '') {
            Html::addCssStyle($options, "width:{$this->width};");
        }
        $options['data-col-seq'] = array_search($this, $this->grid->columns);
        Html::addCssClass($options, $this->grid->options['id']);
        return $options;
    }

    /**
     * Store all rows for the column for the current page
     */
    protected function setPageRows()
    {
        if ($this->grid->showPageSummary && isset($this->pageSummary) && $this->pageSummary !== false &&
            !is_string($this->pageSummary)
        ) {
            $provider = $this->grid->dataProvider;
            $models = array_values($provider->getModels());
            $keys = $provider->getKeys();
            foreach ($models as $index => $model) {
                $key = $keys[$index];
                $this->_rows[] = $this->getDataCellValue($model, $key, $index);
            }
        }
    }

    /**
     * Get the raw footer cell content.
     *
     * @return string the rendered result
     */
    protected function getFooterCellContent()
    {
        return $this->footer;
    }

    /**
     * Initialize column specific JS functionality whenever pjax request completes
     *
     * @param string $script the js script to be used as a callback
     */
    protected function initPjax($script = '')
    {
        if (!$this->grid->pjax || empty($script)) {
            return;
        }
        $cont = 'jQuery("#' . $this->grid->getPjaxContainerId() . '")';
        $view = $this->grid->getView();
        $ev = 'pjax:complete.' . hash('crc32', $script);
        $view->registerJs("{$cont}.off('{$ev}').on('{$ev}', function(){ {$script} });");
    }

    /**
     * Parses a value if Closure and returns the right value
     *
     * @param string|int|Closure $var the variable to parse
     * @param Model $model the model instance
     * @param string|object $key the current model key value
     * @param integer $index the index of the current record in the data provider
     *
     * @return mixed
     */
    protected function parseVal($var, $model, $key, $index)
    {
        return $var instanceof Closure ? call_user_func($var, $model, $key, $index, $this) : $var;
    }

    /**
     * Initializes grid grouping
     */
    protected function initGrouping()
    {
        if (empty($this->group)) {
            return;
        }
        Html::addCssClass($this->headerOptions, ['kv-grid-group-header', $this->grid->options['id']]);
        Html::addCssClass($this->filterOptions, ['kv-grid-group-filter', $this->grid->options['id']]);
        $view = $this->grid->getView();
        $this->headerOptions['data-group-key'] = $this->filterOptions['data-group-key'] = $this->columnKey;
        GridGroupAsset::register($view);
        $id = $this->grid->options['id'];
        $this->_clientScript = "kvGridGroup('{$id}');";
        $view->registerJs($this->_clientScript);
    }

    /**
     * Parses grid grouping and sets data attributes
     *
     * @param array $options
     * @param Model $model
     * @param mixed $key
     * @param integer $index
     */
    protected function parseGrouping(&$options, $model, $key, $index)
    {
        if (empty($this->group)) {
            return;
        }
        Html::addCssClass($options, ['kv-grid-group', $this->grid->options['id']]);
        $options['data-group-key'] = $this->columnKey;
        if (!empty($this->groupOddCssClass)) {
            $options['data-odd-css'] = $this->parseVal($this->groupOddCssClass, $model, $key, $index);
        }
        if (!empty($this->groupEvenCssClass)) {
            $options['data-even-css'] = $this->parseVal($this->groupEvenCssClass, $model, $key, $index);
        }
        if (isset($this->subGroupOf)) {
            $options['data-sub-group-of'] = $this->parseVal($this->subGroupOf, $model, $key, $index);
        }
        if (isset($this->groupedRow)) {
            $options['data-grouped-row'] = $this->parseVal($this->groupedRow, $model, $key, $index);
        }
        if (!empty($this->groupHeader)) {
            $options['data-group-header'] = Json::encode($this->parseVal($this->groupHeader, $model, $key, $index));
        }
        if (!empty($this->groupFooter)) {
            $options['data-group-footer'] = Json::encode($this->parseVal($this->groupFooter, $model, $key, $index));
        }
    }

    /**
     * Initializes the column key
     */
    protected function initColumnKey()
    {
        if (!isset($this->columnKey)) {
            $this->columnKey = hash('crc32', spl_object_hash($this));
        }
    }
    
    /**
     * Filter equal check
     *
     * @param string $class
     * @return bool
     */
    public function isFilterEqual($class) {
        return !empty($this->filterType) && 
            ($this->filterType === $class || $this->filterType === "\\{$class}");
    }
}
