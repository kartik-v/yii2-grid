<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2021
 * @package yii2-grid
 * @version 3.3.6
 */

namespace kartik\grid;

use Closure;
use Exception;
use Yii;
use yii\grid\ActionColumn as YiiActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\base\InvalidConfigException;

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
     * @var boolean whether the action buttons are to be displayed as a dropdown
     */
    public $dropdown = false;

    /**
     * @var array the HTML attributes for the Dropdown container. The class `dropdown` will be added. To align a
     * dropdown at the right edge of the page container, you set the class to `pull-right` for Bootstrap v3.x and
     * for Bootstrap v4.x add `dropdown-menu-right` class in [[dropdownMenu]].
     */
    public $dropdownOptions = [];

    /**
     * @var array the HTML attributes for the Dropdown menu. Applicable if `dropdown` is `true`. To align a
     * dropdown at the right edge of the page container, you set the class to `dropdown-menu-right` for Bootstrap v4.x.
     */
    public $dropdownMenu = ['class' => 'text-left'];

    /**
     * @var array|Closure the dropdown button options. This configuration will be applicable only if [[dropdown]] is
     * `true`. When set as an array, the following special options are recognized:
     *
     * - `label`: _string_', the button label to be displayed. Defaults to `Actions`.
     * - `caret`: _string_', the caret symbol to be appended to the dropdown button. Applicable only for Bootstrap 3.x
     *   versions when `GridView::bsVersion = 3.x`. Defaults to ` <span class="caret"></span>`.
     *
     * This can also be setup as a `Closure` callback function of the following signature that returns the above array:
     *
     * `function ($model, $key, $index) {}`, where:
     *
     * - `$model`: _\yii\db\ActiveRecordInterface_ is the data model of current row
     * - `$key`: _mixed_, is the key associated with the data model
     * - `$index`: _int_, is the current row index
     */
    public $dropdownButton = [];

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
     * @var array the HTML attributes for the header cell tag.
     * @see Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $headerOptions = [];

    /**
     * @var array|Closure the HTML attributes for the data cell tag. This can either be an array of attributes or an
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
     * @throws InvalidConfigException
     */
    public function init()
    {
        $this->initColumnSettings([
            'hiddenFromExport' => true,
            'mergeHeader' => true,
            'hAlign' => GridView::ALIGN_CENTER,
            'vAlign' => GridView::ALIGN_MIDDLE,
            'width' => '100px',
        ]);
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
     * @throws InvalidConfigException
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
     * @throws InvalidConfigException|Exception
     */
    protected function setDefaultButton($name, $title, $icon)
    {
        $notBs3 = !$this->grid->isBs(3);
        if (isset($this->buttons[$name])) {
            return;
        }
        $this->buttons[$name] = function ($url) use ($name, $title, $icon, $notBs3) {
            $opts = "{$name}Options";
            $options = ['title' => $title, 'aria-label' => $title, 'data-pjax' => '0'];
            if ($name === 'delete') {
                $item = $this->grid->itemLabelSingle ?? Yii::t('kvgrid', 'item');
                $options['data-method'] = 'post';
                $options['data-confirm'] = Yii::t('kvgrid', 'Are you sure to delete this {item}?', ['item' => $item]);
            }
            $options = array_replace_recursive($options, $this->buttonOptions, $this->$opts);
            $label = $this->renderLabel($options, $title, ['class' => $this->grid->getDefaultIconPrefix() . $icon, 'aria-hidden' => 'true']);
            $link = Html::a($label, $url, $options);
            if ($this->_isDropdown) {
                $options['tabindex'] = '-1';
                return $notBs3 ? $link : "<li>{$link}</li>\n";
            } else {
                return $link;
            }
        };
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    protected function initDefaultButtons()
    {
        $notBs3 = !$this->grid->isBs(3);
        $this->setDefaultButton('view', Yii::t('kvgrid', 'View'), $notBs3 ? 'eye' : 'eye-open');
        $this->setDefaultButton('update', Yii::t('kvgrid', 'Update'), $notBs3 ? 'pencil-alt' : 'pencil');
        $this->setDefaultButton('delete', Yii::t('kvgrid', 'Delete'), $notBs3 ? 'trash-alt' : 'trash');
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $notBs3 = !$this->grid->isBs(3);
        if ($notBs3 && $this->_isDropdown) {
            Html::addCssClass($this->buttonOptions, 'dropdown-item');
        }
        $content = parent::renderDataCellContent($model, $key, $index);
        $options = $this->dropdownButton;
        if (is_callable($options)) {
            $options = $options($model, $key, $index);
        }
        if (!isset($options['class'])) {
            $options['class'] = 'btn ' . $this->grid->getDefaultBtnCss();
        }
        $trimmed = trim($content);
        if ($this->_isDropdown && !empty($trimmed)) {
            $label = ArrayHelper::remove($options, 'label', Yii::t('kvgrid', 'Actions'));
            $caret = $notBs3 ? '' : ArrayHelper::remove($options, 'caret', ' <span class="caret"></span>');
            $options = array_replace_recursive($options, ['type' => 'button', 'data-toggle' => 'dropdown']);
            Html::addCssClass($options, 'dropdown-toggle');
            $button = Html::button($label . $caret, $options);
            Html::addCssClass($this->dropdownMenu, 'dropdown-menu');
            $dropdown = $button . PHP_EOL . Html::tag($notBs3 ? 'div' : 'ul', $content, $this->dropdownMenu);
            Html::addCssClass($this->dropdownOptions, $notBs3 ? 'btn-group' : 'dropdown');
            return Html::tag('div', $dropdown, $this->dropdownOptions);
        }
        return $content;
    }
}
