<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2016
 * @version   3.1.2
 */

namespace kartik\grid;

use Yii;
use yii\helpers\Html;

/**
 * Extends the Yii's SerialColumn for the Grid widget [[\kartik\widgets\GridView]] with various enhancements.
 *
 * SerialColumn displays a column of row numbers (1-based).
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class SerialColumn extends \yii\grid\SerialColumn
{
    use ColumnTrait;

    /**
     * @var bool whether the column is hidden from display. This is different than the `visible` property, in the
     *     sense, that the column is rendered, but hidden from display. This will allow you to still export the column
     *     using the export function.
     */
    public $hidden;

    /**
     * @var bool|array whether the column is hidden in export output. If set to boolean `true`, it will hide the column
     *     for all export formats. If set as an array, it will accept the list of GridView export `formats` and hide
     *     output only for them.
     */
    public $hiddenFromExport = false;

    /**
     * @var string the horizontal alignment of each column. Should be one of 'left', 'right', or 'center'.
     */
    public $hAlign = GridView::ALIGN_CENTER;

    /**
     * @var string the vertical alignment of each column. Should be one of 'top', 'middle', or 'bottom'.
     */
    public $vAlign = GridView::ALIGN_MIDDLE;

    /**
     * @var bool whether to force no wrapping on all table cells in the column
     * @see http://www.w3schools.com/cssref/pr_text_white-space.asp
     */
    public $noWrap = false;


    /**
     * @var string the width of each column (matches the CSS width property).
     * @see http://www.w3schools.com/cssref/pr_dim_width.asp
     */
    public $width = '50px';

    /**
     * @var bool|string whether the page summary is displayed above the footer for this column. If this is set to a
     *     string, it will be displayed as is. If it is set to `false` the summary will not be calculated and
     *     displayed.
     */
    public $pageSummary = false;

    /**
     * @var string the summary function to call for the column
     */
    public $pageSummaryFunc = GridView::F_SUM;

    /**
     * @var array HTML attributes for the page summary cell
     */
    public $pageSummaryOptions = [];

    /**
     * @var bool whether to just hide the page summary display but still calculate the summary based on `pageSummary`
     *     settings
     */
    public $hidePageSummary = false;

    /**
     * @var bool whether to merge the header title row and the filter row This will not render the filter for the
     *     column and can be used when `filter` is set to `false`. Defaults to `false`. This is only applicable when
     *     `filterPosition` for the grid is set to FILTER_POS_BODY.
     */
    public $mergeHeader = true;

    /**
     * @var string|array in which format should the value of each data model be displayed as (e.g. `"raw"`, `"text"`,
     *     `"html"`, `['date', 'php:Y-m-d']`). Supported formats are determined by the
     *     [[GridView::formatter|formatter]] used by the [[GridView]]. Default format is "text" which will format the
     *     value as an HTML-encoded plain text when [[\yii\i18n\Formatter]] is used as the
     *     [[GridView::$formatter|formatter]] of the GridView.
     */
    public $format = 'text';

    /**
     * @var string the cell format for EXCEL exported content.
     * @see http://cosicimiento.blogspot.in/2008/11/styling-excel-cells-with-mso-number.html
     */
    public $xlFormat;

    /**
     * @var array of row data for the column for the current page
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
