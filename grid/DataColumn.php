<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2013
 * @package yii2-widgets
 * @version 1.0.0
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
    public $halign;

    /**
     * @var string the vertical alignment of each column. Should be one of 
     * 'top', 'middle', or 'bottom'. 
     */
    public $valign;

    /**
     * @var integer the width of each column. 
     * @see `widthUnit`.
     */
    public $width;

    /**
     * @var string the width unit. Can be 'px', 'em', or '%'
     */
    public $widthUnit = 'px';

    /**
     * @var string the filter input type for each input. You can use one of the 
     * `GridView::FILTER_` constants or pass any widget classname (extending the 
     * Yii Input Widget).
     */
    public $filterType;

    /**
     * @var boolean whether to merge the header title row and the filter row
     * This will not render the filter for the column and can be used when `filter`
     * is set to `false`. Defaults to `false`.
     */
    public $mergeHeader = false;

    /**
     * @var boolean whether to show a summary in the footer for this column. This will
     * show the page wise summary only.
     */
    public $summary = true;

    /**
     * @var string the summary function to call for the column
     */
    public $summaryFunc = GridView::F_SUM;

    /**
     * @var array of data for each row in this column that will 
     * be used to calculate the summary
     */
    private $_rows = [];
    private $tmp = 'KV SAYS:-> ';

    public function init()
    {
        if ($this->mergeHeader && !isset($this->valign)) {
            $this->valign = GridView::ALIGN_MIDDLE;
        }
        $this->grid->formatColumn($this->halign, $this->valign, $this->width, $this->widthUnit, $this->format, $this->filterInputOptions, $this->headerOptions, $this->contentOptions, $this->footerOptions);
        $this->setSummaryRows();
        parent::init();
    }

    /**
     * Renders filter inputs based on the `filterType`
     * @return string
     */
    protected function renderFilterCellContent()
    {
        $content = parent::renderFilterCellContent();
        if (empty($this->filterType) || $content === $this->grid->emptyCell) {
            return $content;
        }
        $widgetClass = $this->filterType;
        $options = [
            'model' => $this->grid->filterModel,
            'attribute' => $this->attribute,
            'options' => $this->filterInputOptions
        ];
        if (is_array($this->filter)) {
            $options['data'] = $this->filter;
            if ($this->filterType === GridView::FILTER_RADIO) {
                return Html::activeRadioList($this->grid->filterModel, $this->attribute, $this->filter, $this->filterInputOptions);
            }
            return $widgetClass::widget($options);
        }
        if ($this->filterType === GridView::FILTER_CHECKBOX) {
            return Html::activeCheckbox($this->grid->filterModel, $this->attribute, $this->filterInputOptions);
        }
        return $widgetClass::widget($options);
    }

    /**
     * Renders the header cell.
     */
    public function renderHeaderCell()
    {
        if ($this->grid->filterModel !== null && $this->mergeHeader) {
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
        if ($this->grid->filterModel !== null && $this->mergeHeader) {
            return null;
        }
        return parent::renderFilterCell();
    }

    /**
     * Renders the footer cell content.
     * @return string the rendering result
     */
    protected function renderFooterCellContent()
    {
        if ($this->summary === GridView::SUM_PAGE || $this->summary === GridView::SUM_ALL) {
            $this->footer = $this->grid->formatter->format($this->calculateSummary(), $this->format);
        }
        return parent::renderFooterCellContent();
    }

    protected function setSummaryRows($page = true)
    {
        if ($this->summary) {
            $provider = $this->grid->dataProvider;
            $models = array_values($provider->getModels());
            $keys = $provider->getKeys();
            foreach ($models as $index => $model) {
                $key = $keys[$index];
                $this->_rows[] = $this->getDataCellContent($model, $key, $index);
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
            return null;
        }
        $data = $this->_rows;
        $type = $this->summaryFunc;
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
        return null;
    }

}