<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @package yii2-grid
 * @version 1.6.0
 */

namespace kartik\grid;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

/**
 * Extends the Yii's SerialColumn for the Grid widget [[\kartik\widgets\GridView]]
 * with various enhancements.
 *
 * SerialColumn displays a column of row numbers (1-based).
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class SerialColumn extends \yii\grid\SerialColumn
{

    /**
     * @var string the horizontal alignment of each column. Should be one of
     * 'left', 'right', or 'center'.
     */
    public $hAlign = GridView::ALIGN_CENTER;

    /**
     * @var string the vertical alignment of each column. Should be one of
     * 'top', 'middle', or 'bottom'.
     */
    public $vAlign = GridView::ALIGN_MIDDLE;

    /**
     * @var boolean whether to force no wrapping on all table cells in the column
     * @see http://www.w3schools.com/cssref/pr_text_white-space.asp
     */
    public $noWrap = false;


    /**
     * @var string the width of each column (matches the CSS width property).
     * @see http://www.w3schools.com/cssref/pr_dim_width.asp
     */
    public $width = '50px';

    /**
     * @var boolean|string whether the page summary is displayed above the footer for this column.
     * If this is set to a string, it will be displayed as is. If it is set to `false` the summary
     * will not be calculated and displayed.
     */
    public $pageSummary = false;

    /**
     * @var string the summary function to call for the column
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
     * @var boolean whether to merge the header title row and the filter row
     * This will not render the filter for the column and can be used when `filter`
     * is set to `false`. Defaults to `false`. This is only applicable when `filterPosition`
     * for the grid is set to FILTER_POS_BODY.
     */
    public $mergeHeader = true;

    /**
     * @var array of row data for the column for the current page
     */
    private $_rows = [];

    public function init()
    {
        $this->grid->formatColumn($this->hAlign, $this->vAlign, $this->noWrap, $this->width, $this->headerOptions, $this->contentOptions, $this->pageSummaryOptions, $this->footerOptions);
        parent::init();
        $this->setPageRows();
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
        if ($this->grid->filterPosition === GridView::FILTER_POS_BODY && $this->mergeHeader) {
            return null;
        }
        return parent::renderFilterCell();
    }

    /**
     * Store all rows for the column for the current page
     */
    protected function setPageRows()
    {
        if ($this->grid->showPageSummary === true && isset($this->pageSummary) && $this->pageSummary !== false && !is_string($this->pageSummary)) {
            $provider = $this->grid->dataProvider;
            $models = array_values($provider->getModels());
            $keys = $provider->getKeys();
            foreach ($models as $index => $model) {
                $key = $keys[$index];
                $this->_rows[] = $this->renderDataCellContent($model, $key, $index);
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