<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2015
 * @package yii2-grid
 * @version 3.0.2
 */

namespace kartik\grid;

use Yii;
use yii\base\InvalidConfigException;
use yii\bootstrap\Dropdown;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

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
    use ColumnTrait;

    /**
     * @var boolean whether the column is hidden from display. This is different
     * than the `visible` property, in the sense, that the column is rendered,
     * but hidden from display. This will allow you to still export the column
     * using the export function.
     */
    public $hidden;

    /**
     * @var boolean|array whether the column is hidden in export output. If set to boolean `true`,
     * it will hide the column for all export formats. If set as an array, it will accept the
     * list of GridView export `formats` and hide output only for them.
     */
    public $hiddenFromExport = true;

    /**
     * @var bool whether the action buttons are to be displayed as a dropdown
     */
    public $dropdown = false;

    /**
     * @var array the HTML attributes for the Dropdown container. The class `dropdown` will be added.
     * To align a dropdown at the right edge of the page container, you set the class to `pull-right`.
     */
    public $dropdownOptions = [];

    /**
     * @var array the HTML attributes for the Dropdown menu. Applicable if `dropdown` is `true`.
     */
    public $dropdownMenu = ['class' => 'text-left'];

    /**
     * @var array the dropdown button options. Applicable if `dropdown` is `true`.
     * The following special options are recognized:
     * `label`: the button label to be displayed. Defaults to `Actions`.
     * `caret`: the caret symbol to be appended to the dropdown button.
     *  Defaults to `<span class="caret"></span>`
     */
    public $dropdownButton = ['class' => 'btn btn-default'];

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
    public $headerOptions = [];

    /**
     * @var array|\Closure the HTML attributes for the data cell tag. This can either be an array of
     * attributes or an anonymous function ([[Closure]]) that returns such an array.
     * The signature of the function should be the following: `function ($model, $key, $index, $column)`.
     * A function may be used to assign different attributes to different rows based on the data in that row.
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $contentOptions = [];

    /**
     * @var bool is the dropdown menu to be rendered?
     */
    protected $_isDropdown = false;

    public function init()
    {
        $this->_isDropdown = ($this->grid->bootstrap && $this->dropdown);
        if (!isset($this->header)) {
            $this->header = Yii::t('kvgrid', 'Actions');
        }
        $this->parseFormat();
        $this->parseVisibility();
        parent::init();
        $this->initDefaultButtons();
        $this->setPageRows();
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
                } else {
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
                } else {
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
                $options = ArrayHelper::merge(
                    [
                        'title' => $title,
                        'data-confirm' => Yii::t('kvgrid', 'Are you sure to delete this item?'),
                        'data-method' => 'post',
                        'data-pjax' => '0'
                    ],
                    $options
                );
                if ($this->_isDropdown) {
                    $options['tabindex'] = '-1';
                    return '<li>' . Html::a($label, $url, $options) . '</li>' . PHP_EOL;
                } else {
                    return Html::a($label, $url, $options);
                }
            };
        }
    }

    /**
     * @inheritdoc
     */
    public function renderDataCell($model, $key, $index)
    {
        $options = $this->fetchContentOptions($model, $key, $index);
        return Html::tag('td', $this->renderDataCellContent($model, $key, $index), $options);
    }
    
    /**
     * Renders the data cell.
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $content = parent::renderDataCellContent($model, $key, $index);
        $options = $this->dropdownButton;
        if ($this->_isDropdown) {
            $label = ArrayHelper::remove($options, 'label', Yii::t('kvgrid', 'Actions'));
            $caret = ArrayHelper::remove($options, 'caret', ' <span class="caret"></span>');
            $options = ArrayHelper::merge($options, ['type' => 'button', 'data-toggle' => 'dropdown']);
            Html::addCssClass($options, 'dropdown-toggle');
            $button = Html::button($label . $caret, $options);
            Html::addCssClass($this->dropdownMenu, 'dropdown-menu');
            $dropdown = $button . PHP_EOL . Html::tag('ul', $content, $this->dropdownMenu);
            Html::addCssClass($this->dropdownOptions, 'dropdown');
            return Html::tag('div', $dropdown, $this->dropdownOptions);
        }
        return $content;
    }

}