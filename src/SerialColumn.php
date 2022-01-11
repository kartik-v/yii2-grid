<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2022
 * @version   3.5.0
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
 *         'class' => SerialColumn::class,
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
     * @var array|Closure configuration for the `\kartik\export\ExportMenu` column cell style that will be utilized by
     * `\PhpOffice\PhpSpreadsheet\Style\Style::applyFromArray()`. This is applicable when configuring this column
     * in `\kartik\export\ExportMenu`. If setup as a Closure, the signature of the function should be: `function
     * ($model, $key, $index, $column)`, where `$model`, `$key`, and `$index` refer to the model, key and index of
     * the row currently being rendered, and `$column` is a reference to the [[DataColumn]] object.
     */
    public $exportMenuStyle = ['alignment'=>['vertical' => GridView::ALIGN_CENTER]];

    /**
     * @var array configuration for the `\kartik\export\ExportMenu` column header cell style that will be utilized by
     * `\PhpOffice\PhpSpreadsheet\Style\Style::applyFromArray()`. This is applicable when configuring this column
     * in `\kartik\export\ExportMenu`.
     */
    public $exportMenuHeaderStyle = ['alignment'=>['vertical' => GridView::ALIGN_CENTER]];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->initColumnSettings([
            'mergeHeader' => true,
            'hAlign' => GridView::ALIGN_CENTER,
            'vAlign' => GridView::ALIGN_MIDDLE,
            'width' => '50px'
        ]);
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
