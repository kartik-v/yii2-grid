<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @package yii2-grid
 * @version 1.6.0
 */

namespace kartik\grid;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

/**
 * Extends the Yii's DataColumn for the Grid widget [[\kartik\widgets\GridView]]
 * with various enhancements
 *
 * DataColumn is the default column type for the [[GridView]] widget.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class DataColumn extends \yii\grid\DataColumn
{

    /**
     * @var string the horizontal alignment of each column. Should be one of
     * 'left', 'right', or 'center'.
     */
    public $hAlign;

    /**
     * @var string the vertical alignment of each column. Should be one of
     * 'top', 'middle', or 'bottom'.
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
     * @var string the filter input type for each filter input. You can use one of the
     * `GridView::FILTER_` constants or pass any widget classname (extending the
     * Yii Input Widget).
     */
    public $filterType;

    /**
     * @var array the options/settings for the filter widget. Will be used only if
     * you set `filterType` to a widget classname that exists.
     */
    public $filterWidgetOptions = [];

    /**
     * @var boolean whether to merge the header title row and the filter row
     * This will not render the filter for the column and can be used when `filter`
     * is set to `false`. Defaults to `false`. This is only applicable when `filterPosition`
     * for the grid is set to FILTER_POS_BODY.
     */
    public $mergeHeader = false;

    /**
     * @var boolean|string|Closure the page summary that is displayed above the footer. You can
     * set it to one of the following:
     * - `false`: the summary will not be displayed.
     * - `true`: the page summary for the column will be calculated and displayed using the
     *   `pageSummaryFunc` setting.
     * - any `string`: will be displayed as is
     * - `Closure`: you can set it to an anonymous function with the following signature:
     *   ```
     *   // example 1
     *   function ($summary, $data, $widget) { return 'Count is ' . $summary; }
     *   // example 2
     *   function ($summary, $data, $widget) { return 'Range ' . min($data) . ' to ' . max($data); }
     *   ```
     *   the `$summary` variable will be replaced with the calculated summary using
     *   the `summaryFunc` setting.
     *   the `$data` variable will contain array of the selected page rows for the column.
     */
    public $pageSummary = false;

    /**
     * @var string the summary function used to calculate the page summary for the column
     */
    public $pageSummaryFunc = GridView::F_SUM;

    /**
     * @var array HTML attributes for the page summary cell
     */
    public $pageSummaryOptions = [];

    /**
     * @var boolean whether to just hide the page summary display but still calculate
     * the summary based on `pageSummary` settings
     */
    public $hidePageSummary = false;

    /**
     * @var array of row data for the column for the current page
     */
    private $_rows = [];

    public function init()
    {
        if ($this->mergeHeader && !isset($this->vAlign)) {
            $this->vAlign = GridView::ALIGN_MIDDLE;
        }
        if ($this->grid->bootstrap === false) {
            Html::removeCssClass($filterInputOptions, 'form-control');
        }
        $this->grid->formatColumn($this->hAlign, $this->vAlign, $this->noWrap, $this->width, $this->headerOptions, $this->contentOptions, $this->pageSummaryOptions, $this->footerOptions);
        parent::init();
        $this->setPageRows();
    }

    /**
     * Renders filter inputs based on the `filterType`
     *
     * @return string
     */
    protected function renderFilterCellContent()
    {
        $content = parent::renderFilterCellContent();
        $chkType = !empty($this->filterType) && $this->filterType !== GridView::FILTER_CHECKBOX && $this->filterType !== GridView::FILTER_RADIO && !class_exists($this->filterType);
        if ($this->filter === false || empty($this->filterType) || $content === $this->grid->emptyCell || $chkType) {
            return $content;
        }
        $widgetClass = $this->filterType;
        $options = [
            'model' => $this->grid->filterModel,
            'attribute' => $this->attribute,
            'options' => $this->filterInputOptions
        ];
        if (is_array($this->filter)) {
            if ($this->filterType === GridView::FILTER_SELECT2 || $this->filterType === GridView::FILTER_TYPEAHEAD) {
                $options['data'] = $this->filter;
            }
            if ($this->filterType === GridView::FILTER_RADIO) {
                return Html::activeRadioList($this->grid->filterModel, $this->attribute, $this->filter, $this->filterInputOptions);
            }
        }
        if ($this->filterType === GridView::FILTER_CHECKBOX) {
            return Html::activeCheckbox($this->grid->filterModel, $this->attribute, $this->filterInputOptions);
        }
        $options = ArrayHelper::merge($this->filterWidgetOptions, $options);
        return $widgetClass::widget($options);
    }

    /**
     * Renders the header cell.
     */
    public function renderHeaderCell()
    {
        if ($this->grid->filterModel !== null && $this->mergeHeader && $this->grid->filterPosition === GridView::FILTER_POS_BODY) {
            $this->headerOptions['rowspan'] = 2;
            Html::addCssClass($this->headerOptions, 'kv-merged-header');
        }
        return parent::renderHeaderCell();
    }

    /**
     * Renders the filter cell.
     */
    public function renderFilterCell()
    {
        if ($this->grid->filterModel !== null && $this->mergeHeader && $this->grid->filterPosition === GridView::FILTER_POS_BODY) {
            return null;
        }
        return parent::renderFilterCell();
    }

    /**
     * Store all rows for the column for the current page
     */
    protected function setPageRows()
    {
        if ($this->grid->showPageSummary && isset($this->pageSummary) && $this->pageSummary !== false && !is_string($this->pageSummary)) {
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
     * Calculates the summary of an input data based on aggregration function
     *
     * @param array $data the input data
     * @param string $type the summary aggregation function
     * @return float
     */
    protected function calculateSummary()
    {
        if (empty($this->_rows)) {
            return '';
        }
        $data = $this->_rows;
        $type = $this->pageSummaryFunc;
        switch ($type) {
            case null:
                return array_sum($data);
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
     * Renders the page summary cell.
     */
    public function renderPageSummaryCell()
    {
        return Html::tag('td', $this->renderPageSummaryCellContent(), $this->pageSummaryOptions);
    }

    /**
     * Gets the raw page summary cell content.
     *
     * @return string the rendering result
     */
    protected function getPageSummaryCellContent()
    {
        if ($this->pageSummary === true || $this->pageSummary instanceof \Closure) {
            $summary = $this->calculateSummary();
            return ($this->pageSummary === true) ? $summary : call_user_func($this->pageSummary, $summary, $this->_rows, $this);
        }
        if ($this->pageSummary !== false) {
            return $this->pageSummary;
        }
        return null;
    }

    /**
     * Renders the page summary cell content.
     *
     * @return string the rendering result
     */
    protected function renderPageSummaryCellContent()
    {
        if ($this->hidePageSummary) {
            return $this->grid->emptyCell;
        }
        $content = $this->getPageSummaryCellContent();
        if ($this->pageSummary === true) {
            return $this->grid->formatter->format($content, $this->format);
        }
        return ($content === null) ? $this->grid->emptyCell : $content;
    }

    /**
     * Get the raw footer cell content.
     *
     * @return string the rendering result
     */
    protected function getFooterCellContent()
    {
        return $this->footer;
    }

}