<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2017
 * @version   3.1.8
 */

namespace kartik\grid;

use Closure;
use yii\grid\SerialColumn as YiiSerialColumn;
use yii\helpers\Html;

/**
 * A SerialColumn displays a column of row numbers (1-based) and extends the [[YiiSerialColumn]] with various
 * enhancements.
 *
 * To add a SerialColumn to the gridview, add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         'class' => SerialColumn::className(),
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class SerialColumn extends YiiSerialColumn
{
    use ColumnTrait;

    /**
     * @var boolean whether the column is hidden from display. This is different than the `visible` property, in the
     * sense, that the column is rendered, but hidden from display. This will allow you to still export the column
     * using the export function.
     */
    public $hidden;

    /**
     * @var boolean|array whether the column is hidden in export output. If set to boolean `true`, it will hide the column
     * for all export formats. If set as an array, it will accept the list of GridView export `formats` and hide
     * output only for them.
     */
    public $hiddenFromExport = false;

    /**
     * @var string the horizontal alignment of each column. Should be one of [[GridView::ALIGN_LEFT]],
     * [[GridView::ALIGN_RIGHT]], or [[GridView::ALIGN_CENTER]].
     */
    public $hAlign = GridView::ALIGN_CENTER;

    /**
     * @var string the vertical alignment of each column. Should be one of [[GridView::ALIGN_TOP]],
     * [[GridView::ALIGN_BOTTOM]], or [[GridView::ALIGN_MIDDLE]].
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
     * @var boolean|string|Closure the page summary that is displayed above the footer. You can set it to one of the
     * following:
     * - `false`: the summary will not be displayed.
     * - `true`: the page summary for the column will be calculated and displayed using the
     *   [[pageSummaryFunc]] setting.
     * - `string`: will be displayed as is.
     * - `Closure`: you can set it to an anonymous function with the following signature:
     *
     *   ```php
     *   // example 1
     *   function ($summary, $data, $widget) { return 'Count is ' . $summary; }
     *   // example 2
     *   function ($summary, $data, $widget) { return 'Range ' . min($data) . ' to ' . max($data); }
     *   ```
     *
     *   where:
     *
     *   - the `$summary` variable will be replaced with the calculated summary using the `summaryFunc` setting.
     *   - the `$data` variable will contain array of the selected page rows for the column.
     */
    public $pageSummary = false;

    /**
     * @var string|Closure the summary function that will be used to calculate the page summary for the column. If
     * setting as `Closure`, you can set it to an anonymous function with the following signature:
     *
     * ```php
     * function ($data)
     * ```
     *
     *   - the `$data` variable will contain array of the selected page rows for the column.
     */
    public $pageSummaryFunc = GridView::F_SUM;

    /**
     * @var array HTML attributes for the page summary cell. The following special attributes are available:
     * - `prepend`: _string_, a prefix string that will be prepended before the pageSummary content
     * - `append`: _string_, a suffix string that will be appended after the pageSummary content
     */
    public $pageSummaryOptions = [];

    /**
     * @var boolean whether to just hide the page summary display but still calculate the summary based on
     * [[pageSummary]] settings
     */
    public $hidePageSummary = false;

    /**
     * @var boolean whether to merge the header title row and the filter row This will not render the filter for the
     * column and can be used when `filter` is set to `false`. Defaults to `false`. This is only applicable when
     * [[GridView::filterPosition]] for the grid is set to [[GridView::FILTER_POS_BODY]].
     */
    public $mergeHeader = true;

    /**
     * @var string|array in which format should the value of each data model be displayed as (e.g. `"raw"`, `"text"`,
     * `"html"`, `['date', 'php:Y-m-d']`). Supported formats are determined by the
     * [[GridView::formatter|formatter]] used by the [[GridView]]. Default format is "text" which will format the
     * value as an HTML-encoded plain text when [[\yii\i18n\Formatter]] is used as the
     * [[GridView::$formatter|formatter]] of the GridView.
     */
    public $format = 'text';

    /**
     * @var string the cell format for EXCEL exported content.
     * @see http://cosicimiento.blogspot.in/2008/11/styling-excel-cells-with-mso-number.html
     */
    public $xlFormat;

    /**
     * @var array collection of row data for the column for the current page
     */
    protected $_rows = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->parseFormat();
        $this->parseVisibility();
        parent::init();
        $this->setPageRows();
    }

    /**
     * @inheritdoc
     */
    public function renderDataCell($model, $key, $index)
    {
        $options = $this->fetchContentOptions($model, $key, $index);
        $this->parseExcelFormats($options, $model, $key, $index);
        $out = $this->grid->formatter->format($this->renderDataCellContent($model, $key, $index), $this->format);
        return Html::tag('td', $out, $options);
    }
}
