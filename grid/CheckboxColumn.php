<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @package yii2-grid
 * @version 2.0.0
 */

namespace kartik\grid;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\web\View;

/**
 * Extends the Yii's CheckboxColumn for the Grid widget [[\kartik\widgets\GridView]]
 * with various enhancements.
 *
 * CheckboxColumn displays a column of checkboxes in a grid view.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class CheckboxColumn extends \yii\grid\CheckboxColumn
{
    /**
     * @var boolean whether the column is hidden from display. This is different 
     * than the `visible` property, in the sense, that the column is rendered,
     * but hidden from display. This will allow you to still export the column
     * using the export function.
     */
    public $hidden;
    
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
     * @var boolean highlight current row if checkbox is checked
     */
    public $rowHighlight = true;

    /**
     * @var string highlight CSS class to be applied for highlighting the row.
     * Defaults to 'info'.
     */
    public $rowSelectedClass = GridView::TYPE_INFO;

    /**
     * @var boolean|string whether the page summary is displayed above the footer for this column.
     * If this is set to a string, it will be displayed as is. If it is set to `false` the summary
     * will not be displayed.
     */
    public $pageSummary = false;

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
     * Initializes the widget
     */
    public function init()
    {
        $this->grid->formatColumn($this->hAlign, $this->vAlign, $this->noWrap, $this->width, $this->headerOptions, $this->contentOptions, $this->pageSummaryOptions, $this->footerOptions);
        if ($this->rowHighlight) {
            Html::addCssClass($this->contentOptions, 'kv-row-select');
            Html::addCssClass($this->headerOptions, 'kv-all-select');
            $view = $this->grid->getView();
            $view->registerJs('selectRow("' . $this->grid->options['id'] . '", "' . $this->rowSelectedClass . '");');
        }
        $this->hideColumn();
        parent::init();
    }

    /**
     * Checks `hidden` property and hides the column from display
     */
    protected function hideColumn() {
        if ($this->hidden === true) {
            Html::addCssClass($this->filterOptions, 'kv-hide');
            Html::addCssClass($this->contentOptions, 'kv-hide');
            Html::addCssClass($this->headerOptions, 'kv-hide');
            Html::addCssClass($this->footerOptions, 'kv-hide');
            Html::addCssClass($this->pageSummaryOptions, 'kv-hide');
        }
    }

    /**
     * Initialize column for pjax refresh
     */
    protected function initPjax()
    {
        if ($this->grid->pjax && $this->rowHighlight) {
            $cont = 'jQuery("#' . $this->grid->pjaxSettings['options']['id'] . '")';
            $grid = $this->grid->options['id'];
            $view = $this->grid->getView();
            $view->registerJs("{$cont}.on('pjax:complete', function(){selectRow('{$grid}', '{$this->rowSelectedClass}');});");
        }
    }
    
    /**
     * Renders the data cell content
     */
    public function renderDataCellContent($model, $key, $index)
    {        
        $this->initPjax();
        return parent::renderDataCellContent($model, $key, $index);
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
        return $this->pageSummary === false ? null : $this->pageSummary;
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
        return ($content == null) ? $this->grid->emptyCell : $content;
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