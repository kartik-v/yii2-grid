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
 * Extends the Yii's ActionColumn for the Grid widget [[\kartik\widgets\GridView]]
 * with various enhancements. 
 * 
 * ActionColumn is a column for the [[GridView]] widget that displays buttons 
 * for viewing and manipulating the items.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class ActionColumn extends \yii\grid\ActionColumn
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
    public $width = 80;

    /**
     * @var the width unit. Can be 'px', 'em', or '%'
     */
    public $widthUnit = 'px';

    /**
     * @var array HTML attributes for the view action button. The following additional 
     * option is recognized:
     * `label`: string, the label for the view action button. This is not html encoded.
     */
    public $viewOptions = [];

    /**
     * @var array HTML attributes for the update action button. The following additional 
     * option is recognized:
     * `label`: string, the label for the update action button. This is not html encoded.
     */
    public $updateOptions = [];

    /**
     * @var array HTML attributes for the delete action button. The following additional 
     * option is recognized:
     * `label`: string, the label for the delete action button. This is not html encoded.
     */
    public $deleteOptions = [];

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
        if (!isset($this->header)) {
            $this->header = Yii::t('yii', 'Actions');
        }
        parent::init();
        $this->initDefaultButtons();
    }

    /**
     * Render default action buttons
     * @return string
     */
    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url, $model) {
                $label = ArrayHelper::remove($this->viewOptions, 'label', '<span class="glyphicon glyphicon-eye-open"></span>');
                $this->viewOptions += ['title' => Yii::t('yii', 'View'), 'data-pjax' => '0'];
                return Html::a($label, $url, $this->viewOptions);
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model) {
                $label = ArrayHelper::remove($this->updateOptions, 'label', '<span class="glyphicon glyphicon-pencil"></span>');
                $this->updateOptions += ['title' => Yii::t('yii', 'Update'), 'data-pjax' => '0'];
                return Html::a($label, $url, $this->updateOptions);
            };
        }
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model) {
                $label = ArrayHelper::remove($this->deleteOptions, 'label', '<span class="glyphicon glyphicon-trash"></span>');
                $this->deleteOptions += [
                    'title' => Yii::t('yii', 'Delete'),
                    'data-confirm' => Yii::t('yii', 'Are you sure to delete this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0'
                ];
                return Html::a($label, $url, $this->deleteOptions);
            };
        }
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