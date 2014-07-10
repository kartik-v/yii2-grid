<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @package yii2-grid
 * @version 1.6.0
 */

namespace kartik\grid;

use Yii;
use yii\helpers\Html;
use yii\bootstrap\Dropdown;
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
     * @var bool whether the action buttons are to be displayed as a dropdown
     */
    public $dropdown = false;

    /**
     * @var array the HTML attributes for the Dropdown menu. Applicable if `dropdown` is `true`.
     */
    public $dropdownMenu = ['class'=>'text-left'];

    /**
     * @var array the dropdown button options. Applicable if `dropdown` is `true`.
     * The following special options are recognized:
     * `label`: the button label to be displayed. Defaults to `Actions`.
     * `caret`: the caret symbol to be appended to the dropdown button. 
     *  Defaults to `<span class="caret"></span>`
     */
    public $dropdownButton = ['class'=>'btn btn-default'];
    
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
    public $width = '80px';

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

    /**
     * @var boolean whether to merge the header title row and the filter row
     * This will not render the filter for the column and can be used when `filter`
     * is set to `false`. Defaults to `false`. This is only applicable when `filterPosition`
     * for the grid is set to FILTER_POS_BODY.
     */
    public $mergeHeader = true;
    
    /**
     * @var array the HTML attributes for the header cell tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $headerOptions = ['class'=>'skip-export'];
    
    /**
     * @var array|\Closure the HTML attributes for the data cell tag. This can either be an array of
     * attributes or an anonymous function ([[Closure]]) that returns such an array.
     * The signature of the function should be the following: `function ($model, $key, $index, $column)`.
     * A function may be used to assign different attributes to different rows based on the data in that row.
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $contentOptions = ['class'=>'skip-export'];
    
    /**
     * @var bool is the dropdown menu to be rendered?
     */
    private $_isDropdown = false;

    public function init()
    {
        $this->_isDropdown = ($this->grid->bootstrap && $this->dropdown);
        $this->grid->formatColumn($this->hAlign, $this->vAlign, $this->noWrap, $this->width, $this->headerOptions, $this->contentOptions, $this->pageSummaryOptions, $this->footerOptions);
        if (!isset($this->header)) {
            $this->header = Yii::t('kvgrid', 'Actions');
        }
        parent::init();
        $this->initDefaultButtons();
    }

    /**
     * Render default action buttons
     *
     * @return string
     */
    protected function initDefaultButtons()
    {
        if (!isset($this->buttons['view'])) {
            $this->buttons['view'] = function ($url, $model) {
                $options = $this->viewOptions;
                $title = Yii::t('kvgrid', 'View');
                $icon = '<span class="glyphicon glyphicon-eye-open"></span>';
                $label = ArrayHelper::remove($options, 'label', ($this->_isDropdown ? $icon . ' ' . $title : $icon));
                $options = ArrayHelper::merge(['title' => $title, 'data-pjax' => '0'], $options);
                if ($this->_isDropdown) {
                    $options['tabindex'] = '-1';
                    return '<li>' . Html::a($label, $url, $options) . '</li>' . PHP_EOL;
                }
                else {
                    return Html::a($label, $url, $options);
                }
            };
        }
        if (!isset($this->buttons['update'])) {
            $this->buttons['update'] = function ($url, $model) {
                $options = $this->updateOptions;
                $title = Yii::t('kvgrid', 'Update');
                $icon = '<span class="glyphicon glyphicon-pencil"></span>';
                $label = ArrayHelper::remove($options, 'label', ($this->_isDropdown ? $icon . ' ' . $title : $icon));
                $options = ArrayHelper::merge(['title' => $title, 'data-pjax' => '0'], $options);
                if ($this->_isDropdown) {
                    $options['tabindex'] = '-1';
                    return '<li>' . Html::a($label, $url, $options) . '</li>' . PHP_EOL;
                }
                else {
                    return Html::a($label, $url, $options);
                }
            };
        }
        if (!isset($this->buttons['delete'])) {
            $this->buttons['delete'] = function ($url, $model) {
                $options = $this->deleteOptions;
                $title = Yii::t('kvgrid', 'Delete');
                $icon = '<span class="glyphicon glyphicon-trash"></span>';
                $label = ArrayHelper::remove($options, 'label', ($this->_isDropdown ? $icon . ' ' . $title : $icon));
                $options = ArrayHelper::merge([
                    'title' => $title,
                    'data-confirm' => Yii::t('kvgrid', 'Are you sure to delete this item?'),
                    'data-method' => 'post',
                    'data-pjax' => '0'
                ], $options);
                if ($this->_isDropdown) {
                    $options['tabindex'] = '-1';
                    return '<li>' . Html::a($label, $url, $options) . '</li>' . PHP_EOL;
                }
                else {
                    return Html::a($label, $url, $options);
                }
            };
        }
    }

    /**
     * Renders the data cell.
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $content = parent::renderDataCellContent($model, $key, $index);
        if ($this->_isDropdown) {
            $label = ArrayHelper::remove($this->dropdownButton, 'label', Yii::t('kvgrid', 'Actions'));
            $caret = ArrayHelper::remove($this->dropdownButton, 'caret', ' <span class="caret"></span>');
            $this->dropdownButton = ArrayHelper::merge($this->dropdownButton, ['type'=>'button', 'data-toggle'=>'dropdown']);
            Html::addCssClass($this->dropdownButton, 'dropdown-toggle');
            $button = Html::button($label . $caret, $this->dropdownButton);
            Html::addCssClass($this->dropdownMenu, 'dropdown-menu');
            $dropdown = $button . PHP_EOL . Html::tag('ul', $content, $this->dropdownMenu);
            return Html::tag('div', $dropdown, ['class'=>'dropdown']);
        }
        return $content;
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