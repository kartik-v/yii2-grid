<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2015
 * @version   3.0.2
 */

namespace kartik\grid;

use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

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
    public $hiddenFromExport = false;

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
     * @var array HTML attributes for the page summary cell. The following special attributes
     * are available:
     * - `prepend` string a prefix string that will be prepended before the pageSummary content
     * - `append` string a suffix string that will be appended after the pageSummary content
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
            Html::removeCssClass($this->filterInputOptions, 'form-control');
        }
        $this->parseFormat();
        $this->parseVisibility();
        $this->checkValidFilters();
        parent::init();
        $this->setPageRows();
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
     * Renders filter inputs based on the `filterType`
     *
     * @return string
     */
    protected function renderFilterCellContent()
    {
        $content = parent::renderFilterCellContent();
        $chkType = !empty($this->filterType) && $this->filterType !== GridView::FILTER_CHECKBOX && $this->filterType !== GridView::FILTER_RADIO && !class_exists(
                $this->filterType
            );
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
                return Html::activeRadioList(
                    $this->grid->filterModel,
                    $this->attribute,
                    $this->filter,
                    $this->filterInputOptions
                );
            }
        }
        if ($this->filterType === GridView::FILTER_CHECKBOX) {
            return Html::activeCheckbox($this->grid->filterModel, $this->attribute, $this->filterInputOptions);
        }
        $options = ArrayHelper::merge($this->filterWidgetOptions, $options);
        return $widgetClass::widget($options);
    }
}
