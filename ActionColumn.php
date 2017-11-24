<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2017
 * @package yii2-grid
 * @version 3.1.8
 */

namespace kartik\grid;

use Closure;
use Yii;
use yii\grid\ActionColumn as YiiActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * The ActionColumn is a column that displays buttons for viewing and manipulating the items and extends the
 * [[YiiActionColumn]] with various enhancements.
 *
 * To add an ActionColumn to the gridview, add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         'class' => ActionColumn::className(),
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class ActionColumn extends YiiActionColumn
{
    use ColumnTrait;

    /**
     * @var boolean whether the column is hidden from display. This is different than the `visible` property, in the
     * sense, that the column is rendered, but hidden from display. This will allow you to still export the column
     * using the export function.
     */
    public $hidden;

    /**
     * @var boolean|array whether the column is hidden in export output. If set to boolean `true`, it will hide the
     * column for all export formats. If set as an array, it will accept the list of GridView export `formats` and
     * hide output only for them.
     */
    public $hiddenFromExport = true;

    /**
     * @var boolean whether the action buttons are to be displayed as a dropdown
     */
    public $dropdown = false;

    /**
     * @var array the HTML attributes for the Dropdown container. The class `dropdown` will be added. To align a
     * dropdown at the right edge of the page container, you set the class to `pull-right`.
     */
    public $dropdownOptions = [];

    /**
     * @var array the HTML attributes for the Dropdown menu. Applicable if `dropdown` is `true`.
     */
    public $dropdownMenu = ['class' => 'text-left'];

    /**
     * @var array the dropdown button options. This configuration will be applicable only if [[dropdown]] is `true`.
     * The following special options are recognized:
     *
     * - `label`: _string_', the button label to be displayed. Defaults to `Actions`.
     * - `caret`: _string_', the caret symbol to be appended to the dropdown button.
     *   Defaults to ` <span class="caret"></span>`.
     */
    public $dropdownButton = ['class' => 'btn btn-default'];

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
    public $width = '80px';

    /**
     * @var array HTML attributes for the view action button. The following additional options are recognized:
     * - `label`: _string_, the label for the view action button. This is not html encoded. Defaults to `View`.
     * - `icon`: _null_|_array_|_string_ the icon HTML attributes as an _array_ or the raw icon markup as _string_
     * or _false_ to disable the icon and just use text label instead. When set as a string, this is not HTML
     * encoded. If null or not set, the default icon with CSS `glyphicon glyphicon-eye-open` will be displayed
     * as the icon for the default button.
     */
    public $viewOptions = [];

    /**
     * @var array HTML attributes for the update action button. The following additional options are recognized:
     * - `label`: _string_, the label for the update action button. This is not html encoded. Defaults to `Update`.
     * - `icon`: _null_|_array_|_string_ the icon HTML attributes as an _array_ or the raw icon markup as _string_
     * or _false_ to disable the icon and just use text label instead. When set as a string, this is not HTML
     * encoded. If null or not set, the default icon with CSS `glyphicon glyphicon-pencil` will be displayed
     * as the icon for the default button.
     */
    public $updateOptions = [];

    /**
     * @var array HTML attributes for the delete action button. The following additional options are recognized:
     * - `label`: _string_, the label for the delete action button. This is not html encoded. Defaults to `Delete`.
     * - `icon`: _null_|_array_|_string_ the icon HTML attributes as an _array_ or the raw icon markup as _string_
     * or _false_ to disable the icon and just use text label instead. When set as a string, this is not HTML
     * encoded. If null or not set, the default icon with CSS `glyphicon glyphicon-trash` will be displayed
     * as the icon for the default button.
     * - `data-method`: _string_, the delete HTTP method. Defaults to `post`.
     * - `data-confirm`: _string_, the delete confirmation message to display when the delete button is clicked.
     *   Defaults to `Are you sure to delete this {item}?`, where the `{item}` token will be replaced with the
     *   `GridView::itemLabelSingle` property.
     */
    public $deleteOptions = [];

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
     * @var array the HTML attributes for the header cell tag.
     * @see Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $headerOptions = [];

    /**
     * @var array|\Closure the HTML attributes for the data cell tag. This can either be an array of attributes or an
     * anonymous function ([[Closure]]) that returns such an array. The signature of the function should be the
     * following: `function ($model, $key, $index, $column)`. A function may be used to assign different attributes
     * to different rows based on the data in that row.
     *
     * @see Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $contentOptions = [];

    /**
     * @var boolean is the dropdown menu to be rendered?
     */
    protected $_isDropdown = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        /** @noinspection PhpUndefinedFieldInspection */
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
     * @inheritdoc
     */
    public function renderDataCell($model, $key, $index)
    {
        $options = $this->fetchContentOptions($model, $key, $index);
        return Html::tag('td', $this->renderDataCellContent($model, $key, $index), $options);
    }

    /**
     * Renders button icon
     *
     * @param array $options HTML attributes for the action button element
     * @param array $iconOptions HTML attributes for the icon element. The following additional options are recognized:
     * - `tag`: _string_, the HTML tag to render the icon. Defaults to `span`.
     *
     * @return string
     */
    protected function renderIcon(&$options, $iconOptions = [])
    {
        $icon = ArrayHelper::remove($options, 'icon');
        if ($icon === false) {
            $icon = '';
        } elseif (!is_string($icon)) {
            if (is_array($icon)) {
                $iconOptions = array_replace_recursive($iconOptions, $icon);
            }
            $tag = ArrayHelper::remove($iconOptions, 'tag', 'span');
            $icon = Html::tag($tag, '', $iconOptions);
        }
        return $icon;
    }

    /**
     * Renders button label
     *
     * @param array $options HTML attributes for the action button element
     * @param string $title the action button title
     * @param array $iconOptions HTML attributes for the icon element (see [[renderIcon]])
     *
     * @return string
     */
    protected function renderLabel(&$options, $title, $iconOptions = [])
    {
        $label = ArrayHelper::remove($options, 'label');
        if (is_null($label)) {
            $icon = $this->renderIcon($options, $iconOptions);
            if (strlen($icon) > 0) {
                $label = $this->_isDropdown ? ($icon . ' ' . $title) : $icon;
            } else {
                $label = $title;
            }
        }
        return $label;
    }

    /**
     * Sets a default button configuration based on the button name (bit different than [[initDefaultButton]] method)
     *
     * @param string $name button name as written in the [[template]]
     * @param string $title the title of the button
     * @param string $icon the meaningful glyphicon suffix name for the button
     */
    protected function setDefaultButton($name, $title, $icon)
    {
        if (isset($this->buttons[$name])) {
            return;
        }
        $this->buttons[$name] = function ($url) use ($name, $title, $icon) {
            $opts = "{$name}Options";
            $options = ['title' => $title, 'aria-label' => $title, 'data-pjax' => '0'];
            if ($name === 'delete') {
                $item = isset($this->grid->itemLabelSingle) ? $this->grid->itemLabelSingle : Yii::t('kvgrid', 'item');
                $options['data-method'] = 'post';
                $options['data-confirm'] = Yii::t('kvgrid', 'Are you sure to delete this {item}?', ['item' => $item]);
            }
            $options = array_replace_recursive($options, $this->buttonOptions, $this->$opts);
            $label = $this->renderLabel($options, $title, ['class' => "glyphicon glyphicon-{$icon}"]);
            $link = Html::a($label, $url, $options);
            if ($this->_isDropdown) {
                $options['tabindex'] = '-1';
                return "<li>{$link}</li>\n";
            } else {
                return $link;
            }
        };
    }

    /**
     * @inheritdoc
     */
    protected function initDefaultButtons()
    {
        $this->setDefaultButton('view', Yii::t('kvgrid', 'View'), 'eye-open');
        $this->setDefaultButton('update', Yii::t('kvgrid', 'Update'), 'pencil');
        $this->setDefaultButton('delete', Yii::t('kvgrid', 'Delete'), 'trash');
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $content = parent::renderDataCellContent($model, $key, $index);
        $options = $this->dropdownButton;
        $trimmed = trim($content);
        if ($this->_isDropdown  && !empty($trimmed)) {
            $label = ArrayHelper::remove($options, 'label', Yii::t('kvgrid', 'Actions'));
            $caret = ArrayHelper::remove($options, 'caret', ' <span class="caret"></span>');
            $options = array_replace_recursive($options, ['type' => 'button', 'data-toggle' => 'dropdown']);
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
