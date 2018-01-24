<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2018
 * @version   3.1.8
 */

namespace kartik\grid;

use Yii;
use Closure;
use yii\base\InvalidConfigException;
use yii\grid\Column;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * RadioColumn displays a column of radio inputs in a grid view. It is different than the CheckboxColumn in the sense
 * that it allows only a single row to be selected at a time.
 *
 * To add a RadioColumn to the gridview, add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         'class' => RadioColumn::className(),
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class RadioColumn extends Column
{
    use ColumnTrait;

    /**
     * @var string the name of the radio input fields.
     */
    public $name = 'kvradio';

    /**
     * @var boolean whether to show the clear button in the header to clear the radio.
     */
    public $showClear = true;

    /**
     * @var array the HTML attributes for the clear button in the header. The following special option is recognized:
     * - label: string, the label for the button (defaults to `&times;`);
     */
    public $clearOptions = ['class' => 'close'];

    /**
     * @var array|\Closure the HTML attributes for radio inputs. This can either be an array of attributes or an
     * anonymous function ([[Closure]]) that returns such an array. The signature of the function should be the
     * following: `function ($model, $key, $index, $column)`. Where `$model`, `$key`, and `$index` refer to the
     * model, key and index of the row currently being rendered and `$column` is a reference to the [[RadioColumn]]
     * object. A function may be used to assign different attributes to different rows based on the data in that
     * row. Specifically if you want to set a different value for the radio, you can use this option in the
     * following way (in this example using the `name` attribute of the model):
     *
     * ```php
     * 'radioOptions' => function($model, $key, $index, $column) {
     *     return ['value' => $model->name];
     * }
     * ```
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $radioOptions = [];

    /**
     * @var boolean whether the column is hidden from display. This is different than the `visible` property, in the
     * sense, that the column is rendered, but hidden from display. This will allow you to still export the column
     * using the export function.
     */
    public $hidden;

    /**
     * @var boolean|array whether the column is hidden in export output. If set to bool `true`, it will hide the column
     * for all export formats. If set as an array, it will accept the list of GridView export `formats` and hide
     * output only for them.
     */
    public $hiddenFromExport = true;

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
     * @var boolean highlight current row if checkbox is checked
     */
    public $rowHighlight = true;

    /**
     * @var string highlight CSS class to be applied for highlighting the row. Defaults to 'success'.
     */
    public $rowSelectedClass = GridView::TYPE_SUCCESS;

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
     *   - the `$summary` variable will be replaced with the calculated summary using the [[pageSummaryFunc]] setting.
     *   - the `$data` variable will contain array of the selected page rows for the column.
     */
    public $pageSummary = false;

    /**
     * @var string the summary function that will be used to calculate the page summary for the column.
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
     * @var string the variables for the client script
     */
    protected $_clientVars = '';

    /**
     * @var string the internally generated client script to initialize
     */
    protected $_clientScript = '';

    /**
     * @var string the internally generated column key
     */
    protected $_columnKey = '';

    /**
     * @var View the widget view object instance
     */
    protected $_view;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (empty($this->name)) {
            throw new InvalidConfigException('The "name" property must be set.');
        }
        $css = $this->rowHighlight ? $this->rowSelectedClass : '';
        $this->_view = $this->grid->getView();
        RadioColumnAsset::register($this->_view);
        $grid = $this->grid->options['id'];
        $this->_clientVars = "'{$grid}', '{$this->name}', '{$css}'";
        $this->_clientScript = "kvSelectRadio({$this->_clientVars});";
        $this->_view->registerJs($this->_clientScript);
        $this->parseFormat();
        $this->parseVisibility();
        parent::init();
        $this->setPageRows();
    }

    /**
     * @inheritdoc
     */
    protected function renderHeaderCellContent()
    {
        if ($this->header !== null || !$this->showClear) {
            return parent::renderHeaderCellContent();
        } else {
            $label = ArrayHelper::remove($this->clearOptions, 'label', '&times;');
            Html::addCssClass($this->clearOptions, 'kv-clear-radio');
            if (empty($this->clearOptions['title'])) {
                $this->clearOptions['title'] = Yii::t('kvgrid', 'Clear selection');
            }
            $this->_view->registerJs("kvClearRadio({$this->_clientVars});");
            return Html::button($label, $this->clearOptions);
        }
    }

    /**
     * @inheritdoc
     */
    public function renderDataCell($model, $key, $index)
    {
        $this->initPjax($this->_clientScript);
        $options = $this->fetchContentOptions($model, $key, $index);
        Html::addCssClass($options, 'kv-row-radio-select');
        return Html::tag('td', $this->renderDataCellContent($model, $key, $index), $options);
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if ($this->radioOptions instanceof Closure) {
            $options = call_user_func($this->radioOptions, $model, $key, $index, $this);
        } else {
            $options = $this->radioOptions;
            if (!isset($options['value'])) {
                $options['value'] = is_array($key) ? Json::encode($key) : $key;
            }
        }
        return Html::radio($this->name, !empty($options['checked']), $options);
    }
}
