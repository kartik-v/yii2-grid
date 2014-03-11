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

    public function init()
    {
        $filterOptions = [];
        $this->grid->formatColumn($this->halign, $this->valign, $this->width, $this->widthUnit, null, $filterOptions, $this->headerOptions, $this->contentOptions, $this->footerOptions);
        parent::init();
    }

    /**
     * Renders the header cell.
     */
    public function renderHeaderCell()
    {
        if ($this->grid->filterModel !== null) {
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

}