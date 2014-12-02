<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @package yii2-grid
 * @version 2.7.0
 */

namespace kartik\grid;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use kartik\base\Config;

/**
 * Trait for all column widgets in yii2-grid
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
trait ColumnTrait
{
    /**
     * Checks if the filter input types are valid
     */
    protected function checkValidFilters() {
        if (isset($this->filterType)) {
            Config::validateInputWidget($this->filterType, 'for filtering the grid as per your setup');
        }
    }

    /**
     * Checks `hidden` property and hides the column from display
     */
    protected function parseVisibility() {
        if ($this->hidden === true) {
            Html::addCssClass($this->filterOptions, 'kv-grid-hide');
            Html::addCssClass($this->contentOptions, 'kv-grid-hide');
            Html::addCssClass($this->headerOptions, 'kv-grid-hide');
            Html::addCssClass($this->footerOptions, 'kv-grid-hide');
            Html::addCssClass($this->pageSummaryOptions, 'kv-grid-hide');
        }
        if ($this->hiddenFromExport === true) {
            Html::addCssClass($this->filterOptions, 'skip-export');
            Html::addCssClass($this->contentOptions, 'skip-export');
            Html::addCssClass($this->headerOptions, 'skip-export');
            Html::addCssClass($this->footerOptions, 'skip-export');
            Html::addCssClass($this->pageSummaryOptions, 'skip-export');
            Html::addCssClass($this->options, 'skip-export');
        }
        if (is_array($this->hiddenFromExport) && !empty($this->hiddenFromExport)) {
            $tag = 'skip-export-';
            $css =  $tag . implode(" {$tag}", $this->hiddenFromExport);
            Html::addCssClass($this->filterOptions, $css);
            Html::addCssClass($this->contentOptions, $css);
            Html::addCssClass($this->headerOptions, $css);
            Html::addCssClass($this->footerOptions, $css);
            Html::addCssClass($this->pageSummaryOptions, $css);
            Html::addCssClass($this->options, $css);
        }
    }

    /**
     * Parses and formats a grid column
     */
    protected function parseFormat() {
        if ($this->hAlign === GridView::ALIGN_LEFT || $this->hAlign === GridView::ALIGN_RIGHT || $this->hAlign === GridView::ALIGN_CENTER) {
            $class = "kv-align-{$this->hAlign}";
            Html::addCssClass($this->headerOptions, $class);
            Html::addCssClass($this->contentOptions, $class);
            Html::addCssClass($this->pageSummaryOptions, $class);
            Html::addCssClass($this->footerOptions, $class);
        }
        if ($this->noWrap) {
            Html::addCssClass($this->headerOptions, GridView::NOWRAP);
            Html::addCssClass($this->contentOptions, GridView::NOWRAP);
            Html::addCssClass($this->pageSummaryOptions, GridView::NOWRAP);
            Html::addCssClass($this->footerOptions, GridView::NOWRAP);
        }
        if ($this->vAlign === GridView::ALIGN_TOP || $this->vAlign === GridView::ALIGN_MIDDLE || $this->vAlign === GridView::ALIGN_BOTTOM) {
            $class = "kv-align-{$this->vAlign}";
            Html::addCssClass($this->headerOptions, $class);
            Html::addCssClass($this->contentOptions, $class);
            Html::addCssClass($this->pageSummaryOptions, $class);
            Html::addCssClass($this->footerOptions, $class);
        }
        if (trim($this->width) != '') {
            Html::addCssStyle($this->headerOptions, "width:{$this->width};");
            Html::addCssStyle($this->contentOptions, "width:{$this->width};");
            Html::addCssStyle($this->pageSummaryOptions, "width:{$this->width};");
            Html::addCssStyle($this->footerOptions, "width:{$this->width};");
        }
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
     * Gets the raw page summary cell content.
     *
     * @return string the rendered result
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
     * @return string the rendered result
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
     * @return string the rendered result
     */
    protected function getFooterCellContent()
    {
        return $this->footer;
    }

}