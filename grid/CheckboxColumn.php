<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2013
 * @package yii2-widgets
 * @version 1.0.0
 */

namespace kartik\grid;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

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
     * @var string the horizontal alignment of each column. Should be one of 
     * 'left', 'right', or 'center'. 
     */
    public $halign = GridView::ALIGN_CENTER;

    /**
     * @var string the vertical alignment of each column. Should be one of 
     * 'top', 'middle', or 'bottom'. 
     */
    public $valign = GridView::ALIGN_MIDDLE;

    /**
     * @var integer the width of each column. 
     * @see `widthUnit`.
     */
    public $width = 50;

    /**
     * @var the width unit. Can be 'px', 'em', or '%'
     */
    public $widthUnit = 'px';

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
    
    public function init()
    {
        $this->grid->formatColumn($this->halign, $this->valign, $this->width, $this->widthUnit, null, $this->headerOptions, $this->contentOptions, $this->pageSummaryOptions, $this->footerOptions);
        parent::init();
    }

    /**
     * Renders the header cell.
     */
    public function renderHeaderCell()
    {
        if ($this->grid->filterModel !== null && $this->grid->filterPosition !== GridView::FILTER_POS_FOOTER) {
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
        return null;
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
     * @return string the rendering result
     */
    protected function getPageSummaryCellContent()
    {
        return $this->pageSummary === false ? null : $this->pageSummary;
    }
    
    /**
     * Renders the page summary cell content.
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

    protected function getFooterCellContent() {
         return $this->footer;
    }

}