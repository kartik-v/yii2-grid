<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2017
 * @version   3.1.8
 */

namespace kartik\grid;

use kartik\base\Config;
use kartik\dialog\Dialog;
use Yii;
use yii\base\InvalidConfigException;
use yii\bootstrap\ButtonDropdown;
use yii\grid\Column;
use yii\grid\GridView as YiiGridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\Pjax;

/**
 * The GridView widget is used to display data in a grid. It provides features like [[sorter|sorting]], [[pager|paging]]
 * and also [[filterModel|filtering]] the data.  The [[GridView]] widget extends and modifies [[YiiGridView]] with
 * various new enhancements.
 *
 * The columns of the grid are configured in terms of [[Column]] classes, which are configured via [[columns]]. The look
 * and feel of a grid view can be customized using the large amount of properties.
 *
 * The GridView is available and configurable as part of the Krajee grid [[Module]] with various new additional grid
 * columns and enhanced settings. The extension also incorporates various Bootstrap 3.x styling options, inbuilt
 * additional jQuery plugins and has embedded support for Pjax based rendering.
 *
 * A basic usage of the widget looks like the following:
 *
 * ~~~
 * <?= GridView::widget([
 *     'dataProvider' => $dataProvider,
 *     'columns' => [
 *         'id',
 *         'name',
 *         'created_at:datetime',
 *         // ...
 *     ]
 * ]) ?>
 * ~~~
 *
 * @see http://demos.krajee.com/grid
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since  1.0
 */
class GridView extends YiiGridView
{
    /**
     * The **default** bootstrap contextual color type (applicable only for panel contextual style)
     */
    const TYPE_DEFAULT = 'default';
    /**
     * The **primary** bootstrap contextual color type
     */
    const TYPE_PRIMARY = 'primary';
    /**
     * The **information** bootstrap contextual color type
     */
    const TYPE_INFO = 'info';
    /**
     * The **danger** bootstrap contextual color type
     */
    const TYPE_DANGER = 'danger';
    /**
     * The **warning** bootstrap contextual color type
     */
    const TYPE_WARNING = 'warning';
    /**
     * The **success** bootstrap contextual color type
     */
    const TYPE_SUCCESS = 'success';
    /**
     * The **active** bootstrap contextual color type (applicable only for table row contextual style)
     */
    const TYPE_ACTIVE = 'active';
    /**
     * The **active** icon markup for [[BooleanColumn]]
     */
    const ICON_ACTIVE = '<span class="glyphicon glyphicon-ok text-success"></span>';
    /**
     * The **inactive** icon markup for [[BooleanColumn]]
     */
    const ICON_INACTIVE = '<span class="glyphicon glyphicon-remove text-danger"></span>';
    /**
     * The **expanded** icon markup for [[ExpandRowColumn]]
     */
    const ICON_EXPAND = '<span class="glyphicon glyphicon-expand"></span>';
    /**
     * The **collapsed** icon markup for [[ExpandRowColumn]]
     */
    const ICON_COLLAPSE = '<span class="glyphicon glyphicon-collapse-down"></span>';
    /**
     * The status for a **default** row in [[ExpandRowColumn]]
     */
    const ROW_NONE = -1;
    /**
     * The status for an **expanded** row in [[ExpandRowColumn]]
     */
    const ROW_EXPANDED = 0;
    /**
     * The status for a **collapsed** row in [[ExpandRowColumn]]
     */
    const ROW_COLLAPSED = 1;
    /**
     * Horizontal **right** alignment for grid cells
     */
    const ALIGN_RIGHT = 'right';
    /**
     * Horizontal **center** alignment for grid cells
     */
    const ALIGN_CENTER = 'center';
    /**
     * Horizontal **left** alignment for grid cells
     */
    const ALIGN_LEFT = 'left';
    /**
     * Vertical **top** alignment for grid cells
     */
    const ALIGN_TOP = 'top';
    /**
     * Vertical **middle** alignment for grid cells
     */
    const ALIGN_MIDDLE = 'middle';
    /**
     * Vertical **bottom** alignment for grid cells
     */
    const ALIGN_BOTTOM = 'bottom';
    /**
     * CSS to apply to prevent wrapping of grid cell data
     */
    const NOWRAP = 'kv-nowrap';
    /**
     * Grid filter input type for [[Html::checkbox]]
     */
    const FILTER_CHECKBOX = 'checkbox';
    /**
     * Grid filter input type for [[Html::radio]]
     */
    const FILTER_RADIO = 'radio';
    /**
     * Grid filter input type for [[\kartik\select2\Select2]] widget
     */
    const FILTER_SELECT2 = '\kartik\select2\Select2';
    /**
     * Grid filter input type for [[\kartik\typeahead\Typeahead]] widget
     */
    const FILTER_TYPEAHEAD = '\kartik\typeahead\Typeahead';
    /**
     * Grid filter input type for [[\kartik\switchinput\SwitchInput]] widget
     */
    const FILTER_SWITCH = '\kartik\switchinput\SwitchInput';
    /**
     * Grid filter input type for [[\kartik\touchspin\TouchSpin]] widget
     */
    const FILTER_SPIN = '\kartik\touchspin\TouchSpin';
    /**
     * Grid filter input type for [[\kartik\rating\StarRating]] widget
     */
    const FILTER_STAR = '\kartik\rating\StarRating';
    /**
     * Grid filter input type for [[\kartik\date\DatePicker]] widget
     */
    const FILTER_DATE = '\kartik\date\DatePicker';
    /**
     * Grid filter input type for [[\kartik\time\TimePicker]] widget
     */
    const FILTER_TIME = '\kartik\time\TimePicker';
    /**
     * Grid filter input type for [[\kartik\datetime\DateTimePicker]] widget
     */
    const FILTER_DATETIME = '\kartik\datetime\DateTimePicker';
    /**
     * Grid filter input type for [[\kartik\daterange\DateRangePicker]] widget
     */
    const FILTER_DATE_RANGE = '\kartik\daterange\DateRangePicker';
    /**
     * Grid filter input type for [[\kartik\sortinput\SortableInput]] widget
     */
    const FILTER_SORTABLE = '\kartik\sortinput\SortableInput';
    /**
     * Grid filter input type for [[\kartik\range\RangeInput]] widget
     */
    const FILTER_RANGE = '\kartik\range\RangeInput';
    /**
     * Grid filter input type for [[\kartik\color\ColorInput]] widget
     */
    const FILTER_COLOR = '\kartik\color\ColorInput';
    /**
     * Grid filter input type for [[\kartik\slider\Slider]] widget
     */
    const FILTER_SLIDER = '\kartik\slider\Slider';
    /**
     * Grid filter input type for [[\kartik\money\MaskMoney]] widget
     */
    const FILTER_MONEY = '\kartik\money\MaskMoney';
    /**
     * Grid filter input type for [[\kartik\checkbox\CheckboxX]] widget
     */
    const FILTER_CHECKBOX_X = '\kartik\checkbox\CheckboxX';
    /**
     * Identifier for the `COUNT` summary function
     */
    const F_COUNT = 'f_count';
    /**
     * Identifier for the `SUM` summary function
     */
    const F_SUM = 'f_sum';
    /**
     * Identifier for the `MAX` summary function
     */
    const F_MAX = 'f_max';
    /**
     * Identifier for the `MIN` summary function
     */
    const F_MIN = 'f_min';
    /**
     * Identifier for the `AVG` summary function
     */
    const F_AVG = 'f_avg';
    /**
     * HTML (Hyper Text Markup Language) export format
     */
    const HTML = 'html';
    /**
     * CSV (comma separated values) export format
     */
    const CSV = 'csv';
    /**
     * Text export format
     */
    const TEXT = 'txt';
    /**
     * Microsoft Excel 95+ export format
     */
    const EXCEL = 'xls';
    /**
     * PDF (Portable Document Format) export format
     */
    const PDF = 'pdf';
    /**
     * JSON (Javascript Object Notation) export format
     */
    const JSON = 'json';
    /**
     * Set download target for grid export to a popup browser window
     */
    const TARGET_POPUP = '_popup';
    /**
     * Set download target for grid export to the same open document on the browser
     */
    const TARGET_SELF = '_self';
    /**
     * Set download target for grid export to a new window that auto closes after download
     */
    const TARGET_BLANK = '_blank';

    /**
     * @var string the module identifier if this widget is part of a module. If not set, the module identifier will
     * be auto derived based on the \yii\base\Module::getInstance method. This can be useful, if you are setting
     * multiple module identifiers for the same module in your Yii configuration file. To specify children or grand
     * children modules you can specify the module identifiers relative to the parent module (e.g. `admin/content`).
     */
    public $moduleId;

    /**
     * @var array configuration settings for the Krajee dialog widget that will be used to render alerts and
     * confirmation dialog prompts
     * @see http://demos.krajee.com/dialog
     */
    public $krajeeDialogSettings = [];

    /**
     * @var string the layout that determines how different sections of the list view should be organized.
     * The layout template will be automatically set based on the [[panel]] setting. If [[panel]] is a valid
     * array, then the [[layout]] will default to the [[panelTemplate]] property. If the [[panel]] property
     * is set to `false`, then the [[layout]] will default to `{summary}\n{items}\n{pager}`.
     *
     * The following tokens will be replaced with the corresponding section contents:
     *
     * - `{summary}`: the summary section. See [[renderSummary()]].
     * - `{errors}`: the filter model error summary. See [[renderErrors()]].
     * - `{items}`: the list items. See [[renderItems()]].
     * - `{sorter}`: the sorter. See [[renderSorter()]].
     * - `{pager}`: the pager. See [[renderPager()]].
     * - `{export}`: the grid export button menu. See [[renderExport()]].
     * - `{toolbar}`: the grid panel toolbar. See [[renderToolbar()]].
     * - `{toolbarContainer}`: the toolbar container. See [[renderToolbarContainer()]].
     *
     * In addition to the above tokens, refer the [[panelTemplate]] property for other tokens supported as
     * part of the bootstrap styled panel.
     *
     */
    public $layout = "{summary}\n{items}\n{pager}";

    /**
     * @var string the default label shown for each record in the grid (singular). This label will replace the singular
     * word `item` within the grid summary text as well as ActionColumn default delete confirmation message.
     */
    public $itemLabelSingle;

    /**
     * @var string the default label shown for each record in the grid (plural). This label will replace the plural word
     * `items` within the grid summary text.
     */
    public $itemLabelPlural;

    /**
     * @var string the default label shown for each record in the grid (plural). Similar to [[itemLabelPlural]] but
     * this is applicable for languages like russian, where the plural label can be different for fewer item count.
     * This label will replace the plural word `items-few` within the grid summary text.
     */
    public $itemLabelFew;

    /**
     * @var string the default label shown for each record in the grid (plural). Similar to [[itemLabelPlural]] but
     * this is applicable for languages like russian, where the plural label can be different for many item count.
     * This label will replace the plural word `items-many` within the grid summary text.
     */
    public $itemLabelMany;

    /**
     * @var string the template for rendering the grid within a bootstrap styled panel.
     * The following special tokens are recognized and will be replaced:
     * - `{prefix}`: _string_, the CSS prefix name as set in [[panelPrefix]]. Defaults to `panel panel-`.
     * - `{type}`: _string_, the panel type that will append the bootstrap contextual CSS.
     * - `{panelHeading}`: _string_, which will render the panel heading block.
     * - `{panelBefore}`: _string_, which will render the panel before block.
     * - `{panelAfter}`: _string_, which will render the panel after block.
     * - `{panelFooter}`: _string_, which will render the panel footer block.
     * - `{items}`: _string_, which will render the grid items.
     * - `{summary}`: _string_, which will render the grid results summary.
     * - `{pager}`: _string_, which will render the grid pagination links.
     * - `{toolbar}`: _string_, which will render the [[toolbar]] property passed
     * - `{toolbarContainer}`: _string_, which will render the toolbar container. See [[renderToolbarContainer()]].
     * - `{export}`: _string_, which will render the [[export]] menu button content.
     */
    public $panelTemplate = <<< HTML
<div class="{prefix}{type}">
    {panelHeading}
    {panelBefore}
    {items}
    {panelAfter}
    {panelFooter}
</div>
HTML;

    /**
     * @var string the template for rendering the panel heading. The following special tokens are
     * recognized and will be replaced:
     * - `{heading}`: _string_, which will render the panel heading content.
     * - `{summary}`: _string_, which will render the grid results summary.
     * - `{items}`: _string_, which will render the grid items.
     * - `{pager}`: _string_, which will render the grid pagination links.
     * - `{sort}`: _string_, which will render the grid sort links.
     * - `{toolbar}`: _string_, which will render the [[toolbar]] property passed
     * - `{toolbarContainer}`: _string_, which will render the toolbar container. See [[renderToolbarContainer()]].
     * - `{export}`: _string_, which will render the [[export]] menu button content.
     */
    public $panelHeadingTemplate = <<< HTML
    <div class="pull-right">
        {summary}
    </div>
    <h3 class="panel-title">
        {heading}
    </h3>
    <div class="clearfix"></div>
HTML;

    /**
     * @var string the template for rendering the panel footer. The following special tokens are
     * recognized and will be replaced:
     * - `{footer}`: _string_, which will render the panel footer content.
     * - `{summary}`: _string_, which will render the grid results summary.
     * - `{items}`: _string_, which will render the grid items.
     * - `{sort}`: _string_, which will render the grid sort links.
     * - `{pager}`: _string_, which will render the grid pagination links.
     * - `{toolbar}`: _string_, which will render the [[toolbar]] property passed
     * - `{export}`: _string_, which will render the [[export]] menu button content
     */
    public $panelFooterTemplate = <<< HTML
    <div class="kv-panel-pager">
        {pager}
    </div>
    {footer}
    <div class="clearfix"></div>
HTML;

    /**
     * @var string the template for rendering the `{before} part in the layout templates.
     * The following special tokens are recognized and will be replaced:
     * - `{before}`: _string_, which will render the [[before]] text passed in the panel settings
     * - `{summary}`: _string_, which will render the grid results summary.
     * - `{items}`: _string_, which will render the grid items.
     * - `{sort}`: _string_, which will render the grid sort links.
     * - `{pager}`: _string_, which will render the grid pagination links.
     * - `{toolbar}`: _string_, which will render the [[toolbar]] property passed
     * - `{toolbarContainer}`: _string_, which will render the toolbar container. See [[renderToolbarContainer()]].
     * - `{export}`: _string_, which will render the [[export]] menu button content
     */
    public $panelBeforeTemplate = <<< HTML
    {toolbarContainer}
    {before}
    <div class="clearfix"></div>
HTML;

    /**
     * @var string the template for rendering the `{after} part in the layout templates. The following special
     * variables are recognized and will be replaced:
     * - `{after}`: _string_, which will render the `after` text passed within the [[panel]] settings
     * - `{summary}`: _string_, which will render the grid results summary.
     * - `{items}`: _string_, which will render the grid items.
     * - `{sort}`: _string_, which will render the grid sort links.
     * - `{pager}`: _string_, which will render the grid pagination links.
     * - `{toolbar}`: _string_, which will render the [[toolbar]] property passed
     * - `{toolbarContainer}`: _string_, which will render the toolbar container. See [[renderToolbarContainer()]].
     * - `{export}`: _string_, which will render the [[export]] menu button content
     */
    public $panelAfterTemplate = '{after}';

    /**
     * @var string the panel CSS prefix that will be applied to the panel container for rendering the grid
     * within a bootstrap styled panel. This can be set to a different value to generate different styles for
     * other bootstrap themes. For example, this can be set to `box box-` for rendering boxes in AdminLTE theme.
     */
    public $panelPrefix = 'panel panel-';

    /**
     * @var array the panel settings for displaying the grid view within a bootstrap styled panel. This property is
     * therefore applicable only if [[bootstrap]] property is `true`. The following array keys can be configured:
     * - `type`: _string_, the panel contextual type. Set it to one of the TYPE constants. If not set, will default to
     *   [[TYPE_DEFAULT]].
     * - `heading`: `string`|`boolean`, the panel heading. If set to `false`, will not be displayed.
     * - `headingOptions`: _array_, HTML attributes for the panel heading container. Defaults to
     *   `['class'=>'panel-heading']`.
     * - `footer`: `string`|`boolean`, the panel footer. If set to `false` will not be displayed.
     * - `footerOptions`: _array_, HTML attributes for the panel footer container. Defaults to
     *   `['class'=>'panel-footer']`.
     * - 'before': `string`|`boolean`, content to be placed before/above the grid (after the header). To not display
     *   this section, set this to `false`.
     * - `beforeOptions`: _array_, HTML attributes for the `before` text. If the `class` is not set, it will default to
     *   `kv-panel-before`.
     * - 'after': `string`|`boolean`, any content to be placed after/below the grid (before the footer). To not
     *   display this section, set this to `false`.
     * - `afterOptions`: _array_, HTML attributes for the `after` text. If the `class` is not set, it will default to
     *   `kv-panel-after`.
     */
    public $panel = [];

    /**
     * @var array|string configuration of additional header table rows that will be rendered before the default grid
     * header row. If set as a _string_, it will be displayed as is, without any HTML encoding. If set as an _array_,
     * each row in this array corresponds to a HTML table row, where you can configure the columns with these properties:
     * - `columns`: _array_, the header row columns configuration where you can set the following properties:
     *    - `content`: _string_, the grid cell content for the column
     *    - `tag`: _string_, the tag for rendering the grid cell. If not set, defaults to `th`.
     *    - `options`: _array_, the HTML attributes for the grid cell
     * - `options`: _array_, the HTML attributes for the table row
     */
    public $beforeHeader = [];

    /**
     * @var array|string configuration of additional header table rows that will be rendered after default grid header
     * row. If set as a _string_, it will be displayed as is, without any HTML encoding. If set as an _array_, each
     * row in this array corresponds to a HTML table row, where you can configure the columns with these properties:
     * - `columns`: _array_, the header row columns configuration where you can set the following properties:
     *    - `content`: _string_, the grid cell content for the column
     *    - `tag`: _string_, the tag for rendering the grid cell. If not set, defaults to `th`.
     *    - `options`: _array_, the HTML attributes for the grid cell
     * - `options`: _array_, the HTML attributes for the table row
     */
    public $afterHeader = [];

    /**
     * @var array|string configuration of additional footer table rows that will be rendered before the default grid
     * footer row. If set as a _string_, it will be displayed as is, without any HTML encoding. If set as an _array_,
     * each row in this array corresponds to a HTML table row, where you can configure the columns with these properties:
     * - `columns`: _array_, the footer row columns configuration where you can set the following properties:
     *    - `content`: _string_, the grid cell content for the column
     *    - `tag`: _string_, the tag for rendering the grid cell. If not set, defaults to `th`.
     *    - `options`: _array_, the HTML attributes for the grid cell
     * - `options`: _array_, the HTML attributes for the table row
     */
    public $beforeFooter = [];

    /**
     * @var array|string configuration of additional footer table rows that will be rendered after the default grid
     * footer row. If set as a _string_, it will be displayed as is, without any HTML encoding. If set as an _array_,
     * each row in this array corresponds to a HTML table row, where you can configure the columns with these properties:
     * - `columns`: _array_, the footer row columns configuration where you can set the following properties:
     *    - `content`: _string_, the grid cell content for the column
     *    - `tag`: _string_, the tag for rendering the grid cell. If not set, defaults to `th`.
     *    - `options`: _array_, the HTML attributes for the grid cell
     * - `options`: _array_, the HTML attributes for the table row
     */
    public $afterFooter = [];

    /**
     * @var array|string the toolbar content configuration. Can be setup as a string or an array. When set as a
     * _string_, it will be rendered as is. When set as an _array_, each line item will be considered as per the
     * following rules:
     * - if the line item is setup as a _string_, it will be rendered as is
     * - if the line item is an _array_, the following keys can be setup to control the rendering of the toolbar:
     *     - `content`: _string_, the content to be rendered as a bootstrap button group. The following special tags
     *       in the content are recognized and will be replaced:
     *         - `{export}`, _string_ which will render the [[export]] menu button content.
     *         - `{toggleData}`, _string_ which will render the button to toggle between page data and all data.
     *         - `options`: _array_, the HTML attributes for the button group div container. By default the CSS class
     *           `btn-group` will be attached to this container if no class is set.
     */
    public $toolbar = [
        '{toggleData}',
        '{export}',
    ];

    /**
     * @var array the HTML attributes for the toolbar container. The following special attributes are recognized:
     *
     * - `tag`: _string_, the HTML tag to render the toolbar container. Defaults to `div`.
     */
    public $toolbarContainerOptions = ['class' => 'btn-toolbar kv-grid-toolbar toolbar-container pull-right'];

    /**
     * @var array tags to replace in the rendered layout. Enter this as `$key => $value` pairs, where:
     * - `$key`: _string_, defines the flag.
     * - `$value`: _string_|_Closure_, the value that will be replaced. You can set it as a callback function to return
     *   a string of the signature: `function ($widget) { return 'custom'; }`.
     *
     * For example, a custom tag like `{star}` can be set as:
     *
     * ```php
     * [
     *     '{star}' => '<span class="glyphicon glyphicon-asterisk"></span>'
     * ]
     * ```
     */
    public $replaceTags = [];

    /**
     * @var string the default data column class if the class name is not explicitly specified when configuring a data
     * column. Defaults to 'kartik\grid\DataColumn'.
     */
    public $dataColumnClass = 'kartik\grid\DataColumn';

    /**
     * @var array the HTML attributes for the grid footer row
     */
    public $footerRowOptions = ['class' => 'kv-table-footer'];

    /**
     * @var array the HTML attributes for the grid caption
     */
    public $captionOptions = ['class' => 'kv-table-caption'];

    /**
     * @var array the HTML attributes for the grid element
     */
    public $tableOptions = [];

    /**
     * @var boolean whether the grid view will be rendered within a pjax container. Defaults to `false`. If set to
     * `true`, the entire GridView widget will be parsed via Pjax and auto-rendered inside a yii\widgets\Pjax
     * widget container. If set to `false` pjax will be disabled and none of the pjax settings will be applied.
     */
    public $pjax = false;

    /**
     * @var array the pjax settings for the widget. This will be considered only when [[pjax]] is set to true. The
     * following settings are recognized:
     * - `neverTimeout`: `boolean`, whether the pjax request should never timeout. Defaults to `true`. The pjax:timeout
     *   event will be configured to disable timing out of pjax requests for the pjax container.
     * - `options`: _array_, the options for the [[\yii\widgets\Pjax]] widget.
     * - `loadingCssClass`: boolean/string, the CSS class to be applied to the grid when loading via pjax. If set to
     *   `false` - no css class will be applied. If it is empty, null, or set to `true`, will default to
     *   `kv-grid-loading`.
     * - `beforeGrid`: _string_, any content to be embedded within pjax container before the Grid widget.
     * - `afterGrid`: _string_, any content to be embedded within pjax container after the Grid widget.
     */
    public $pjaxSettings = [];

    /**
     * @var boolean whether to allow resizing of columns
     */
    public $resizableColumns = true;

    /**
     * @var boolean whether to hide resizable columns for smaller screen sizes (< 768px). Defaults to `true`.
     */
    public $hideResizeMobile = true;

    /**
     * @var array the resizableColumns plugin options
     */
    public $resizableColumnsOptions = ['resizeFromBody' => false];

    /**
     * @var boolean whether to store resized column state using local storage persistence (supported by most modern
     * browsers).
     */
    public $persistResize = false;

    /**
     * @var string resizable unique storage prefix to append to the grid id. If empty or not set it will default to
     * `Yii::$app->user->id`.
     */
    public $resizeStorageKey;

    /**
     * @var boolean whether the grid view will have Bootstrap table styling.
     */
    public $bootstrap = true;

    /**
     * @var boolean whether the grid will have a `bordered` style. Applicable only if `bootstrap` is `true`.
     */
    public $bordered = true;

    /**
     * @var boolean whether the grid will have a `striped` style. Applicable only if `bootstrap` is `true`.
     */
    public $striped = true;

    /**
     * @var boolean whether the grid will have a `condensed` style. Applicable only if `bootstrap` is `true`.
     */
    public $condensed = false;

    /**
     * @var boolean whether the grid will have a `responsive` style. Applicable only if `bootstrap` is `true`.
     */
    public $responsive = true;

    /**
     * @var boolean whether the grid will automatically wrap to fit columns for smaller display sizes.
     */
    public $responsiveWrap = true;

    /**
     * @var boolean whether the grid will highlight row on `hover`. Applicable only if `bootstrap` is `true`.
     */
    public $hover = false;

    /**
     * @var boolean whether the grid will have a floating table header.
     */
    public $floatHeader = false;

    /**
     * @var boolean whether the table header will float and sticks around as you scroll within a container. If
     * `responsive` is true then this is auto set to `true`.
     */
    public $floatOverflowContainer = false;

    /**
     * @var array the plugin options for the floatThead plugin that would render the floating/sticky table header
     * behavior. The default offset from the top of the window where the floating header will 'stick' when scrolling
     * down is set to `50` assuming a fixed bootstrap navbar on top. You can set this to `0` or any javascript
     * function / expression.
     * @see http://mkoryak.github.io/floatThead#options
     */
    public $floatHeaderOptions = ['top' => 50];

    /**
     * @var boolean whether pretty perfect scrollbars using perfect scrollbar plugin is to be used. Defaults to
     * `false`. If this is set to true, the `floatOverflowContainer` property will be auto set to `true`, if
     * `floatHeader` is `true`.
     * @see https://github.com/noraesae/perfect-scrollbar
     */
    public $perfectScrollbar = false;

    /**
     * @var array the plugin options for the perfect scrollbar plugin.
     * @see https://github.com/noraesae/perfect-scrollbar
     */
    public $perfectScrollbarOptions = [];

    /**
     * @var boolean whether to show the page summary row for the table. This will be displayed above the footer.
     */
    public $showPageSummary = false;

    /**
     * @array the HTML attributes for the page summary container. The following special options are recognized:
     *
     * - `tag`: _string_, the tag used to render the page summary. Defaults to `tbody`.
     */
    public $pageSummaryContainer = ['class' => 'kv-page-summary-container'];

    /**
     * @array the HTML attributes for the summary row.
     */
    public $pageSummaryRowOptions = ['class' => 'kv-page-summary warning'];

    /**
     * @var string the default pagination that will be read by toggle data. Should be one of 'page' or 'all'. If not
     * set to 'all', it will always defaults to 'page'.
     */
    public $defaultPagination = 'page';

    /**
     * @var boolean whether to enable toggling of grid data. Defaults to `true`.
     */
    public $toggleData = true;

    /**
     * @var array the settings for the toggle data button for the toggle data type. This will be setup as an
     * associative array of $key => $value pairs, where $key can be:
     * - `maxCount`: `int`|`boolean`, the maximum number of records uptil which the toggle button will be rendered. If
     *   the dataProvider records exceed this setting, the toggleButton will not be displayed. Defaults to `10000` if
     *   not set. If you set this to `true`, the toggle button will always be displayed. If you set this to `false
     *   the toggle button will not be displayed (similar to `toggleData` setting).
     * - `minCount`: `int`|`boolean`, the minimum number of records beyond which a confirmation message will be
     *   displayed when toggling all records. If the dataProvider record count exceeds this setting, a confirmation
     *   message will be alerted to the user. Defaults to `500` if not set. If you set this to `true`, the
     *   confirmation message will always be displayed. If set to `false` no confirmation message will be displayed.
     * - `confirmMsg`: _string_, the confirmation message for the toggle data when `minCount` threshold is exceeded.
     *   Defaults to `'There are {totalCount} records. Are you sure you want to display them all?'`.
     * - `all`: _array_, configuration for showing all grid data and the value is the HTML attributes for the button.
     *   (refer `page` for understanding the default options).
     * - `page`: _array_, configuration for showing first page data and $options is the HTML attributes for the button.
     *    The following special options are recognized:
     *    - `icon`: _string_, the glyphicon suffix name. If not set or empty will not be displayed.
     *    - `label`: _string_, the label for the button.
     *
     *      This defaults to the following setting:
     *
     *      ```php
     *      [
     *          'maxCount' => 10000,
     *          'minCount' => 1000
     *          'confirmMsg' => Yii::t(
     *              'kvgrid',
     *              'There are {totalCount} records. Are you sure you want to display them all?',
     *              ['totalCount' => number_format($this->dataProvider->getTotalCount())]
     *          ),
     *          'all' => [
     *              'icon' => 'resize-full',
     *              'label' => 'All',
     *              'class' => 'btn btn-default',
     *              'title' => 'Show all data'
     *          ],
     *          'page' => [
     *              'icon' => 'resize-small',
     *              'label' => 'Page',
     *              'class' => 'btn btn-default',
     *              'title' => 'Show first page data'
     *          ],
     *      ]
     *      ```
     */
    public $toggleDataOptions = [];

    /**
     * @var array the HTML attributes for the toggle data button group container. By default this will always have the
     * `class = btn-group` automatically added, if no class is set.
     */
    public $toggleDataContainer = [];

    /**
     * @var array the HTML attributes for the export button group container. By default this will always have the
     * `class = btn-group` automatically added, if no class is set.
     */
    public $exportContainer = [];

    /**
     * @var array|boolean the grid export menu settings. Displays a Bootstrap dropdown menu that allows you to export the
     * grid as either html, csv, or excel. If set to `false`, will not be displayed. The following options can be
     * set:
     * - `icon`: _string_,the glyphicon suffix to be displayed before the export menu label. If not set or is an empty
     *   string, this will not be displayed. Defaults to `export` if `fontAwesome` is `false` and `share-square-o` if
     *   fontAwesome is `true`.
     * - `label`: _string_,the export menu label (this is not HTML encoded). Defaults to ''.
     * - `showConfirmAlert`: bool, whether to show a confirmation alert dialog before download. This confirmation
     *   dialog will notify user about the type of exported file for download and to disable popup blockers.
     *   Defaults to `true`.
     * - `target`: _string_, the target for submitting the export form, which will trigger
     *   the download of the exported file. Must be one of the `TARGET_` constants.
     *   Defaults to `GridView::TARGET_POPUP`.
     * - `messages`: _array_, the configuration of various messages that will be displayed at runtime:
     *     - `allowPopups`: _string_, the message to be shown to disable browser popups for download.
     *       Defaults to `Disable any popup blockers in your browser to ensure proper download.`.
     *     - `confirmDownload`: _string_, the message to be shown for confirming to proceed with the download. Defaults
     *       to `Ok to proceed?`.
     *     - `downloadProgress`: _string_, the message to be shown in a popup dialog when download request is
     *       triggered. Defaults to `Generating file. Please wait...`.
     *     - `downloadComplete`: _string_, the message to be shown in a popup dialog when download request is completed.
     *       Defaults to `All done! Click anywhere here to close this window, once you have downloaded the file.`.
     * - `header`: _string_, the header for the page data export dropdown. If set to empty string will not be
     *   displayed. Defaults to: `<li role="presentation" class="dropdown-header">Export Page Data</li>`.
     * - `fontAwesome`: bool, whether to use font awesome file type icons. Defaults to `false`. If you set it to
     *   `true`, then font awesome icons css class will be applied instead of glyphicons.
     * - `itemsBefore`: _array_, any additional items that will be merged/prepended before with the export dropdown
     *   list. This should be similar to the `items` property as supported by `\yii\bootstrap\ButtonDropdown` widget.
     *   Note the page export items will be automatically generated based on settings in the `exportConfig` property.
     * - `itemsAfter`: _array_, any additional items that will be merged/appended after with the export dropdown list.
     *   This should be similar to the `items` property as supported by `\yii\bootstrap\ButtonDropdown` widget. Note
     *   the page export items will be automatically generated based on settings in the `exportConfig` property.
     * - `options`: _array_, HTML attributes for the export menu button. Defaults to
     *   `['class' => 'btn btn-default', 'title'=>'Export']`.
     * - `encoding`: _string_, the export output file encoding. If not set, defaults to `utf-8`.
     * - `bom`: `boolean`, whether a BOM is to be embedded for text or CSV files with utf-8 encoding. Defaults to
     *   `true`.
     * - `menuOptions`: _array_, HTML attributes for the export dropdown menu. Defaults to `['class' => 'dropdown-menu
     *   dropdown-menu-right']`. This property is to be setup exactly as the `options` property required by the
     *   [[\yii\bootstrap\Dropdown]] widget.
     */
    public $export = [];

    /**
     * @var array the configuration for each export format. The array keys must be the one of the `format` constants
     * (CSV, HTML, TEXT, EXCEL, PDF, JSON) and the array value is a configuration array consisiting of these settings:
     * - `label`: _string_,the label for the export format menu item displayed
     * - `icon`: _string_,the glyphicon or font-awesome name suffix to be displayed before the export menu item label.
     *   If set to an empty string, this will not be displayed. Refer `defaultConfig` in `initExport` method for
     *   default settings.
     * - `showHeader`: `boolean`, whether to show table header row in the output. Defaults to `true`.
     * - `showPageSummary`: `boolean`, whether to show table page summary row in the output. Defaults to `true`.
     * - `showFooter`: `boolean`, whether to show table footer row in the output. Defaults to `true`.
     * - `showCaption`: `boolean`, whether to show table caption in the output (only for HTML). Defaults to `true`.
     * - `filename`: the base file name for the generated file. Defaults to 'grid-export'. This will be used to
     *   generate a default file name for downloading (extension will be one of csv, html, or xls - `based on the
     *   format setting).
     * - `alertMsg`: _string_, the message prompt to show before saving. If this is empty or not set it will not be
     *   displayed.
     * - `options`: _array_, HTML attributes for the export format menu item.
     * - `mime`: _string_, the mime type (for the file format) to be set before downloading.
     * - `config`: _array_, the special configuration settings specific to each file format/type. The following
     *   configuration options are read specific to each file type:
     *     - `HTML`: The following properties can be set as array key-value pairs:
     *          - `cssFile`: _string_, the css file that will be used in the exported HTML file. Defaults to:
     *            `https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css`.
     *     - `CSV` and `TEXT`: The following properties can be set as array key-value pairs:
     *          - `colDelimiter`: _string_, the column delimiter string for TEXT and CSV downloads.
     *          - `rowDelimiter`: _string_, the row delimiter string for TEXT and CSV downloads.
     *     - `EXCEL`: The following properties can be set as array key-value pairs:
     *          - `worksheet`: _string_, the name of the worksheet, when saved as EXCEL file.
     *     - `PDF`: Supports all configuration properties as required in [[\kartik\mpdf\Pdf]] extension. In addition, the
     *       following additional special options are recognized:
     *          - `contentBefore`: _string_, any HTML formatted content that will be embedded in the PDF output before
     *            the grid.
     *          - `contentAfter`: _string_, any HTML formatted content that will be embedded in the PDF output after
     *            the grid.
     *     - `JSON`: The following properties can be set as array key-value pairs:
     *          - `colHeads`: _array_, the column heading names to be output in the json file. If not set, it will be
     *            autogenerated as "col-{i}", where {i} is the column index. If `slugColHeads` is set to `true`, the
     *            extension will attempt to autogenerate column heads based on table column heading, whereever
     *     possible.
     *          - `slugColHeads`: `boolean`, whether to auto-generate column identifiers as slugs based on the table
     *            column heading name. If the table column heading contains characters which cannot be slugified, then
     *            the extension will autogenerate the column name as "col-{i}".
     *          - `jsonReplacer``: array|JsExpression, the JSON replacer property - `can be an array or a JS function
     *            created using JsExpression. Refer the [JSON documentation](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide/Using_native_JSON#The_replacer_parameter)
     *            for details on setting this property.
     *          - `indentSpace`: int, pretty print json output and indent by number of spaces specified. Defaults to `4`.
     */
    public $exportConfig = [];

    /**
     * @var array conversion of defined patterns in the grid cells as a preprocessing before the gridview is formatted
     * for export. Each array row must consist of the following two keys:
     * - `from`: _string_, is the pattern to search for in each grid column's cells
     * - `to`: _string_, is the string to replace the pattern in the grid column cells
     * This defaults to:
     * ```php
     * [
     *      ['from'=>GridView::ICON_ACTIVE, 'to'=>Yii::t('kvgrid', 'Active')],
     *      ['from'=>GridView::ICON_INACTIVE, 'to'=>Yii::t('kvgrid', 'Inactive')]
     * ]
     * ```
     */
    public $exportConversions = [];

    /**
     * @var boolean determines whether the exported EXCEL cell data will be automatically guessed and formatted based on
     * [[DataColumn::format]] property. This property is applicable for EXCEL export content only. One can override this
     * behavior and change the auto-derived format mask by setting [[DataColumn::xlFormat]].
     */
    public $autoXlFormat = false;

    /**
     * @var array|boolean the HTML attributes for the grid container. The grid items will be wrapped in a `div`
     * container with the configured HTML attributes. The ID for the container will be auto generated.
     */
    public $containerOptions = [];

    /**
     * @var string the generated client script for the grid
     */
    protected $_gridClientFunc = '';

    /**
     * @var Module the grid module.
     */
    protected $_module;

    /**
     * @var string key to identify showing all data
     */
    protected $_toggleDataKey;

    /**
     * @var string HTML attribute identifier for the toggle button
     */
    protected $_toggleButtonId;

    /**
     * @var string the JS variable to store the toggle options
     */
    protected $_toggleOptionsVar;

    /**
     * @var string generated plugin script for the toggle button
     */
    protected $_toggleScript;

    /**
     * @var boolean whether the current mode is showing all data
     */
    protected $_isShowAll = false;

    /**
     * Parses export configuration and returns the merged defaults.
     *
     * @param array $exportConfig the export configuration
     * @param array $defaultExportConfig the default export configuration
     *
     * @return array
     */
    protected static function parseExportConfig($exportConfig, $defaultExportConfig)
    {
        if (is_array($exportConfig) && !empty($exportConfig)) {
            foreach ($exportConfig as $format => $setting) {
                $setup = is_array($exportConfig[$format]) ? $exportConfig[$format] : [];
                $exportConfig[$format] = empty($setup) ? $defaultExportConfig[$format] :
                    array_replace_recursive($defaultExportConfig[$format], $setup);
            }
            return $exportConfig;
        }
        return $defaultExportConfig;
    }

    /**
     * Sets a default css class within `options` if not set
     *
     * @param array $options the HTML options
     * @param string $css the CSS class to test and append
     */
    protected static function initCss(&$options, $css)
    {
        if (!isset($options['class'])) {
            $options['class'] = $css;
        }
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->initModule();
        if (empty($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        if (!$this->toggleData) {
            parent::init();
            return;
        }
        $this->_toggleDataKey = '_tog' . hash('crc32', $this->options['id']);
        $this->_isShowAll = ArrayHelper::getValue($_GET, $this->_toggleDataKey, $this->defaultPagination) === 'all';
        if ($this->_isShowAll) {
            /** @noinspection PhpUndefinedFieldInspection */
            $this->dataProvider->pagination = false;
        }
        $this->_toggleButtonId = $this->options['id'] . '-togdata-' . ($this->_isShowAll ? 'all' : 'page');
        if (!isset($this->itemLabelSingle)) {
            $this->itemLabelSingle = Yii::t('kvgrid', 'item');
        }
        if (!isset($this->itemLabelPlural)) {
            $this->itemLabelPlural = Yii::t('kvgrid', 'items');
        }
        if (!isset($this->itemLabelFew)) {
            $this->itemLabelFew = Yii::t('kvgrid', 'items-few');
        }
        if (!isset($this->itemLabelMany)) {
            $this->itemLabelMany = Yii::t('kvgrid', 'items-many');
        }
        parent::init();
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function run()
    {
        $this->initToggleData();
        $this->initExport();
        if ($this->export !== false && isset($this->exportConfig[self::PDF])) {
            Config::checkDependency(
                'mpdf\Pdf',
                'yii2-mpdf',
                'for PDF export functionality. To include PDF export, follow the install steps below. If you do not ' .
                "need PDF export functionality, do not include 'PDF' as a format in the 'export' property. You can " .
                "otherwise set 'export' to 'false' to disable all export functionality"
            );
        }
        $this->initHeader();
        $this->initBootstrapStyle();
        $this->containerOptions['id'] = $this->options['id'] . '-container';
        Html::addCssClass($this->containerOptions, 'kv-grid-container');
        $this->registerAssets();
        $this->renderPanel();
        $this->initLayout();
        $this->beginPjax();
        parent::run();
        $this->endPjax();
    }

    /**
     * Renders the table page summary.
     *
     * @return string the rendering result.
     */
    public function renderPageSummary()
    {
        if (!$this->showPageSummary) {
            return null;
        }
        $cells = [];
        /** @var DataColumn $column */
        foreach ($this->columns as $column) {
            $cells[] = $column->renderPageSummaryCell();
        }
        $tag = ArrayHelper::remove($this->pageSummaryContainer, 'tag', 'tbody');
        $content = Html::tag('tr', implode('', $cells), $this->pageSummaryRowOptions);
        return Html::tag($tag, $content, $this->pageSummaryContainer);
    }

    /**
     * @inheritdoc
     */
    public function renderTableBody()
    {
        $content = parent::renderTableBody();
        if ($this->showPageSummary) {
            return $content . $this->renderPageSummary();
        }
        return $content;
    }

    /**
     * Renders the toggle data button.
     *
     * @return string
     */
    public function renderToggleData()
    {
        if (!$this->toggleData) {
            return '';
        }
        $maxCount = ArrayHelper::getValue($this->toggleDataOptions, 'maxCount', false);
        if ($maxCount !== true && (!$maxCount || (int)$maxCount <= $this->dataProvider->getTotalCount())) {
            return '';
        }
        $tag = $this->_isShowAll ? 'page' : 'all';
        $options = $this->toggleDataOptions[$tag];
        $label = ArrayHelper::remove($options, 'label', '');
        $url = Url::current([$this->_toggleDataKey => $tag]);
        static::initCss($this->toggleDataContainer, 'btn-group');
        return Html::tag('div', Html::a($label, $url, $options), $this->toggleDataContainer);
    }

    /**
     * Renders the export menu.
     *
     * @return string
     */
    public function renderExport()
    {
        if ($this->export === false || !is_array($this->export) ||
            empty($this->exportConfig) || !is_array($this->exportConfig)
        ) {
            return '';
        }
        $title = $this->export['label'];
        $icon = $this->export['icon'];
        $options = $this->export['options'];
        $menuOptions = $this->export['menuOptions'];
        $iconPrefix = $this->export['fontAwesome'] ? 'fa fa-' : 'glyphicon glyphicon-';
        $title = ($icon == '') ? $title : "<i class='{$iconPrefix}{$icon}'></i> {$title}";
        if (!isset($this->_module->downloadAction)) {
            $action = ["/{$this->moduleId}/export/download"];
        } else {
            $action = (array)$this->_module->downloadAction;
        }
        $encoding = ArrayHelper::getValue($this->export, 'encoding', 'utf-8');
        $bom = ArrayHelper::getValue($this->export, 'bom', true);
        $target = ArrayHelper::getValue($this->export, 'target', self::TARGET_POPUP);
        $formOptions = [
            'class' => 'kv-export-form',
            'style' => 'display:none',
            'target' => ($target == self::TARGET_POPUP) ? 'kvDownloadDialog' : $target,
        ];
        $form = Html::beginForm($action, 'post', $formOptions) . "\n" .
            Html::hiddenInput('module_id', $this->moduleId) . "\n" .
            Html::hiddenInput('export_hash') . "\n" .
            Html::hiddenInput('export_filetype') . "\n" .
            Html::hiddenInput('export_filename') . "\n" .
            Html::hiddenInput('export_mime') . "\n" .
            Html::hiddenInput('export_config') . "\n" .
            Html::hiddenInput('export_encoding', $encoding) . "\n" .
            Html::hiddenInput('export_bom', $bom) . "\n" .
            Html::textarea('export_content') . "\n" .
            Html::endForm();
        $items = empty($this->export['header']) ? [] : [$this->export['header']];
        foreach ($this->exportConfig as $format => $setting) {
            $iconOptions = ArrayHelper::getValue($setting, 'iconOptions', []);
            Html::addCssClass($iconOptions, $iconPrefix . $setting['icon']);
            $label = (empty($setting['icon']) || $setting['icon'] == '') ? $setting['label'] :
                Html::tag('i', '', $iconOptions) . ' ' . $setting['label'];
            $mime = ArrayHelper::getValue($setting, 'mime', 'text/plain');
            $config = ArrayHelper::getValue($setting, 'config', []);
            if ($format === self::JSON) {
                unset($config['jsonReplacer']);
            }
            $dataToHash = $this->moduleId . $setting['filename'] . $mime . $encoding . $bom . Json::encode($config);
            $hash = Yii::$app->security->hashData($dataToHash, $this->_module->exportEncryptSalt);
            $items[] = [
                'label' => $label,
                'url' => '#',
                'linkOptions' => [
                    'class' => 'export-' . $format,
                    'data-mime' => $mime,
                    'data-hash' => $hash,
                ],
                'options' => $setting['options'],
            ];
        }
        $itemsBefore = ArrayHelper::getValue($this->export, 'itemsBefore', []);
        $itemsAfter = ArrayHelper::getValue($this->export, 'itemsAfter', []);
        $items = ArrayHelper::merge($itemsBefore, $items, $itemsAfter);
        return ButtonDropdown::widget(
                [
                    'label' => $title,
                    'dropdown' => ['items' => $items, 'encodeLabels' => false, 'options' => $menuOptions],
                    'options' => $options,
                    'containerOptions' => $this->exportContainer,
                    'encodeLabel' => false,
                ]
            ) . $form;
    }

    /**
     * @inheritdoc
     */
    public function renderTableHeader()
    {
        $cells = [];
        foreach ($this->columns as $index => $column) {
            /* @var DataColumn $column */
            if ($this->resizableColumns && $this->persistResize) {
                $column->headerOptions['data-resizable-column-id'] = "kv-col-{$index}";
            }
            $cells[] = $column->renderHeaderCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->headerRowOptions);
        if ($this->filterPosition == self::FILTER_POS_HEADER) {
            $content = $this->renderFilters() . $content;
        } elseif ($this->filterPosition == self::FILTER_POS_BODY) {
            $content .= $this->renderFilters();
        }
        return "<thead>\n" .
            $this->generateRows($this->beforeHeader) . "\n" .
            $content . "\n" .
            $this->generateRows($this->afterHeader) . "\n" .
            '</thead>';
    }

    /**
     * @inheritdoc
     */
    public function renderTableFooter()
    {
        $content = parent::renderTableFooter();
        return strtr(
            $content,
            [
                '<tfoot>' => "<tfoot>\n" . $this->generateRows($this->beforeFooter),
                '</tfoot>' => $this->generateRows($this->afterFooter) . "\n</tfoot>",
            ]
        );
    }

    /**
     * @inheritdoc
     */
    public function renderColumnGroup()
    {
        $requireColumnGroup = false;
        foreach ($this->columns as $column) {
            /* @var $column Column */
            if (!empty($column->options)) {
                $requireColumnGroup = true;
                break;
            }
        }
        if ($requireColumnGroup) {
            $cols = [];
            foreach ($this->columns as $column) {
                //Skip column with groupedRow
                /** @noinspection PhpUndefinedFieldInspection */
                if (property_exists($column, 'groupedRow') && $column->groupedRow) {
                    continue;
                }
                $cols[] = Html::tag('col', '', $column->options);
            }

            return Html::tag('colgroup', implode("\n", $cols));
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function renderSummary()
    {
        $count = $this->dataProvider->getCount();
        if ($count <= 0) {
            return '';
        }
        $summaryOptions = $this->summaryOptions;
        $tag = ArrayHelper::remove($summaryOptions, 'tag', 'div');
        if (($pagination = $this->dataProvider->getPagination()) !== false) {
            $totalCount = $this->dataProvider->getTotalCount();
            $begin = $pagination->getPage() * $pagination->pageSize + 1;
            $end = $begin + $count - 1;
            if ($begin > $end) {
                $begin = $end;
            }
            $page = $pagination->getPage() + 1;
            $pageCount = $pagination->pageCount;
            if (($summaryContent = $this->summary) === null) {
                return Html::tag($tag, Yii::t('kvgrid',
                    'Showing <b>{begin, number}-{end, number}</b> of <b>{totalCount, number}</b> {totalCount, plural, one{{item}} other{{items}}}.',
                    [
                        'begin' => $begin,
                        'end' => $end,
                        'count' => $count,
                        'totalCount' => $totalCount,
                        'page' => $page,
                        'pageCount' => $pageCount,
                        'item' => $this->itemLabelSingle,
                        'items' => $this->itemLabelPlural,
                        'items-few' => $this->itemLabelFew,
                        'items-many' => $this->itemLabelMany,
                    ]), $summaryOptions);
            }
        } else {
            $begin = $page = $pageCount = 1;
            $end = $totalCount = $count;
            if (($summaryContent = $this->summary) === null) {
                return Html::tag($tag,
                    Yii::t('kvgrid', 'Total <b>{count, number}</b> {count, plural, one{{item}} other{{items}}}.', [
                        'begin' => $begin,
                        'end' => $end,
                        'count' => $count,
                        'totalCount' => $totalCount,
                        'page' => $page,
                        'pageCount' => $pageCount,
                        'item' => $this->itemLabelSingle,
                        'items' => $this->itemLabelPlural,
                        'items-few' => $this->itemLabelFew,
                        'items-many' => $this->itemLabelMany,
                    ]), $summaryOptions);
            }
        }

        return Yii::$app->getI18n()->format($summaryContent, [
            'begin' => $begin,
            'end' => $end,
            'count' => $count,
            'totalCount' => $totalCount,
            'page' => $page,
            'pageCount' => $pageCount,
        ], Yii::$app->language);
    }

    /**
     * Initialize the module based on module identifier
     */
    protected function initModule()
    {
        if (!isset($this->moduleId)) {
            $this->_module = Module::getInstance();
            if (isset($this->_module)) {
                $this->moduleId = $this->_module->id;
                return;
            }
            $this->moduleId = Module::MODULE;
        }
        $this->_module = Config::getModule($this->moduleId, Module::className());
    }

    /**
     * Initialize grid export.
     */
    protected function initExport()
    {
        if ($this->export === false) {
            return;
        }
        $this->exportConversions = array_replace_recursive(
            [
                ['from' => self::ICON_ACTIVE, 'to' => Yii::t('kvgrid', 'Active')],
                ['from' => self::ICON_INACTIVE, 'to' => Yii::t('kvgrid', 'Inactive')],
            ],
            $this->exportConversions
        );
        if (!isset($this->export['fontAwesome'])) {
            $this->export['fontAwesome'] = false;
        }
        $isFa = $this->export['fontAwesome'];
        $this->export = array_replace_recursive(
            [
                'label' => '',
                'icon' => $isFa ? 'share-square-o' : 'export',
                'messages' => [
                    'allowPopups' => Yii::t(
                        'kvgrid',
                        'Disable any popup blockers in your browser to ensure proper download.'
                    ),
                    'confirmDownload' => Yii::t('kvgrid', 'Ok to proceed?'),
                    'downloadProgress' => Yii::t('kvgrid', 'Generating the export file. Please wait...'),
                    'downloadComplete' => Yii::t(
                        'kvgrid',
                        'Request submitted! You may safely close this dialog after saving your downloaded file.'
                    ),
                ],
                'options' => ['class' => 'btn btn-default', 'title' => Yii::t('kvgrid', 'Export')],
                'menuOptions' => ['class' => 'dropdown-menu dropdown-menu-right '],
            ],
            $this->export
        );
        if (!isset($this->export['header'])) {
            $this->export['header'] = '<li role="presentation" class="dropdown-header">' .
                Yii::t('kvgrid', 'Export Page Data') . '</li>';
        }
        if (!isset($this->export['headerAll'])) {
            $this->export['headerAll'] = '<li role="presentation" class="dropdown-header">' .
                Yii::t('kvgrid', 'Export All Data') . '</li>';
        }
        $title = empty($this->caption) ? Yii::t('kvgrid', 'Grid Export') : $this->caption;
        $pdfHeader = [
            'L' => [
                'content' => Yii::t('kvgrid', 'Yii2 Grid Export (PDF)'),
                'font-size' => 8,
                'color' => '#333333',
            ],
            'C' => [
                'content' => $title,
                'font-size' => 16,
                'color' => '#333333',
            ],
            'R' => [
                'content' => Yii::t('kvgrid', 'Generated') . ': ' . date('D, d-M-Y g:i a T'),
                'font-size' => 8,
                'color' => '#333333',
            ],
        ];
        $pdfFooter = [
            'L' => [
                'content' => Yii::t('kvgrid', '© Krajee Yii2 Extensions'),
                'font-size' => 8,
                'font-style' => 'B',
                'color' => '#999999',
            ],
            'R' => [
                'content' => '[ {PAGENO} ]',
                'font-size' => 10,
                'font-style' => 'B',
                'font-family' => 'serif',
                'color' => '#333333',
            ],
            'line' => true,
        ];
        $defaultExportConfig = [
            self::HTML => [
                'label' => Yii::t('kvgrid', 'HTML'),
                'icon' => $isFa ? 'file-text' : 'floppy-saved',
                'iconOptions' => ['class' => 'text-info'],
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => Yii::t('kvgrid', 'grid-export'),
                'alertMsg' => Yii::t('kvgrid', 'The HTML export file will be generated for download.'),
                'options' => ['title' => Yii::t('kvgrid', 'Hyper Text Markup Language')],
                'mime' => 'text/html',
                'config' => [
                    'cssFile' => 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css',
                ],
            ],
            self::CSV => [
                'label' => Yii::t('kvgrid', 'CSV'),
                'icon' => $isFa ? 'file-code-o' : 'floppy-open',
                'iconOptions' => ['class' => 'text-primary'],
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => Yii::t('kvgrid', 'grid-export'),
                'alertMsg' => Yii::t('kvgrid', 'The CSV export file will be generated for download.'),
                'options' => ['title' => Yii::t('kvgrid', 'Comma Separated Values')],
                'mime' => 'application/csv',
                'config' => [
                    'colDelimiter' => ',',
                    'rowDelimiter' => "\r\n",
                ],
            ],
            self::TEXT => [
                'label' => Yii::t('kvgrid', 'Text'),
                'icon' => $isFa ? 'file-text-o' : 'floppy-save',
                'iconOptions' => ['class' => 'text-muted'],
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => Yii::t('kvgrid', 'grid-export'),
                'alertMsg' => Yii::t('kvgrid', 'The TEXT export file will be generated for download.'),
                'options' => ['title' => Yii::t('kvgrid', 'Tab Delimited Text')],
                'mime' => 'text/plain',
                'config' => [
                    'colDelimiter' => "\t",
                    'rowDelimiter' => "\r\n",
                ],
            ],
            self::EXCEL => [
                'label' => Yii::t('kvgrid', 'Excel'),
                'icon' => $isFa ? 'file-excel-o' : 'floppy-remove',
                'iconOptions' => ['class' => 'text-success'],
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => Yii::t('kvgrid', 'grid-export'),
                'alertMsg' => Yii::t('kvgrid', 'The EXCEL export file will be generated for download.'),
                'options' => ['title' => Yii::t('kvgrid', 'Microsoft Excel 95+')],
                'mime' => 'application/vnd.ms-excel',
                'config' => [
                    'worksheet' => Yii::t('kvgrid', 'ExportWorksheet'),
                    'cssFile' => '',
                ],
            ],
            self::PDF => [
                'label' => Yii::t('kvgrid', 'PDF'),
                'icon' => $isFa ? 'file-pdf-o' : 'floppy-disk',
                'iconOptions' => ['class' => 'text-danger'],
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => Yii::t('kvgrid', 'grid-export'),
                'alertMsg' => Yii::t('kvgrid', 'The PDF export file will be generated for download.'),
                'options' => ['title' => Yii::t('kvgrid', 'Portable Document Format')],
                'mime' => 'application/pdf',
                'config' => [
                    'mode' => 'UTF-8',
                    'format' => 'A4-L',
                    'destination' => 'D',
                    'marginTop' => 20,
                    'marginBottom' => 20,
                    'cssInline' => '.kv-wrap{padding:20px;}' .
                        '.kv-align-center{text-align:center;}' .
                        '.kv-align-left{text-align:left;}' .
                        '.kv-align-right{text-align:right;}' .
                        '.kv-align-top{vertical-align:top!important;}' .
                        '.kv-align-bottom{vertical-align:bottom!important;}' .
                        '.kv-align-middle{vertical-align:middle!important;}' .
                        '.kv-page-summary{border-top:4px double #ddd;font-weight: bold;}' .
                        '.kv-table-footer{border-top:4px double #ddd;font-weight: bold;}' .
                        '.kv-table-caption{font-size:1.5em;padding:8px;border:1px solid #ddd;border-bottom:none;}',
                    'methods' => [
                        'SetHeader' => [
                            ['odd' => $pdfHeader, 'even' => $pdfHeader],
                        ],
                        'SetFooter' => [
                            ['odd' => $pdfFooter, 'even' => $pdfFooter],
                        ],
                    ],
                    'options' => [
                        'title' => $title,
                        'subject' => Yii::t('kvgrid', 'PDF export generated by kartik-v/yii2-grid extension'),
                        'keywords' => Yii::t('kvgrid', 'krajee, grid, export, yii2-grid, pdf'),
                    ],
                    'contentBefore' => '',
                    'contentAfter' => '',
                ],
            ],
            self::JSON => [
                'label' => Yii::t('kvgrid', 'JSON'),
                'icon' => $isFa ? 'file-code-o' : 'floppy-open',
                'iconOptions' => ['class' => 'text-warning'],
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => Yii::t('kvgrid', 'grid-export'),
                'alertMsg' => Yii::t('kvgrid', 'The JSON export file will be generated for download.'),
                'options' => ['title' => Yii::t('kvgrid', 'JavaScript Object Notation')],
                'mime' => 'application/json',
                'config' => [
                    'colHeads' => [],
                    'slugColHeads' => false,
                    'jsonReplacer' => new JsExpression("function(k,v){return typeof(v)==='string'?$.trim(v):v}"),
                    'indentSpace' => 4,
                ],
            ],
        ];

        // Remove PDF if dependency is not loaded.
        if (!class_exists('\\kartik\\mpdf\\Pdf')) {
            unset($defaultExportConfig[self::PDF]);
        }

        $this->exportConfig = self::parseExportConfig($this->exportConfig, $defaultExportConfig);
    }

    /**
     * Initialize toggle data button options.
     */
    protected function initToggleData()
    {
        if (!$this->toggleData) {
            return;
        }
        $defaultOptions = [
            'maxCount' => 10000,
            'minCount' => 500,
            'confirmMsg' => Yii::t(
                'kvgrid',
                'There are {totalCount} records. Are you sure you want to display them all?',
                ['totalCount' => number_format($this->dataProvider->getTotalCount())]
            ),
            'all' => [
                'icon' => 'resize-full',
                'label' => Yii::t('kvgrid', 'All'),
                'class' => 'btn btn-default',
                'title' => Yii::t('kvgrid', 'Show all data'),
            ],
            'page' => [
                'icon' => 'resize-small',
                'label' => Yii::t('kvgrid', 'Page'),
                'class' => 'btn btn-default',
                'title' => Yii::t('kvgrid', 'Show first page data'),
            ],
        ];
        $this->toggleDataOptions = array_replace_recursive($defaultOptions, $this->toggleDataOptions);
        $tag = $this->_isShowAll ? 'page' : 'all';
        $options = $this->toggleDataOptions[$tag];
        $this->toggleDataOptions[$tag]['id'] = $this->_toggleButtonId;
        $icon = ArrayHelper::remove($this->toggleDataOptions[$tag], 'icon', '');
        $label = !isset($options['label']) ? $defaultOptions[$tag]['label'] : $options['label'];
        if (!empty($icon)) {
            $label = "<i class='glyphicon glyphicon-{$icon}'></i> " . $label;
        }
        $this->toggleDataOptions[$tag]['label'] = $label;
        if (!isset($this->toggleDataOptions[$tag]['title'])) {
            $this->toggleDataOptions[$tag]['title'] = $defaultOptions[$tag]['title'];
        }
        $this->toggleDataOptions[$tag]['data-pjax'] = $this->pjax ? 'true' : false;
    }

    /**
     * Initialize bootstrap specific styling.
     */
    protected function initBootstrapStyle()
    {
        Html::addCssClass($this->tableOptions, 'kv-grid-table');
        if (!$this->bootstrap) {
            return;
        }
        Html::addCssClass($this->tableOptions, 'table');
        if ($this->hover) {
            Html::addCssClass($this->tableOptions, 'table-hover');
        }
        if ($this->bordered) {
            Html::addCssClass($this->tableOptions, 'table-bordered');
        }
        if ($this->striped) {
            Html::addCssClass($this->tableOptions, 'table-striped');
        }
        if ($this->condensed) {
            Html::addCssClass($this->tableOptions, 'table-condensed');
        }
        if ($this->floatHeader) {
            if ($this->perfectScrollbar) {
                $this->floatOverflowContainer = true;
            }
            if ($this->floatOverflowContainer) {
                $this->responsive = false;
                Html::addCssClass($this->containerOptions, 'kv-grid-wrapper');
            }
        }
        if ($this->responsive) {
            Html::addCssClass($this->containerOptions, 'table-responsive');
        }
        if ($this->responsiveWrap) {
            Html::addCssClass($this->tableOptions, 'kv-table-wrap');
        }
    }

    /**
     * Initialize table header.
     */
    protected function initHeader()
    {
        if ($this->filterPosition === self::FILTER_POS_HEADER) {
            // Float header plugin misbehaves when filter is placed on the first row.
            // So disable it when `filterPosition` is `header`.
            $this->floatHeader = false;
        }
    }

    /**
     * Initialize the grid layout.
     */
    protected function initLayout()
    {
        Html::addCssClass($this->filterRowOptions, 'skip-export');
        if ($this->resizableColumns && $this->persistResize) {
            $key = empty($this->resizeStorageKey) ? Yii::$app->user->id : $this->resizeStorageKey;
            $gridId = empty($this->options['id']) ? $this->getId() : $this->options['id'];
            $this->containerOptions['data-resizable-columns-id'] = (empty($key) ? "kv-{$gridId}" : "kv-{$key}-{$gridId}");
        }
        if ($this->hideResizeMobile) {
            Html::addCssClass($this->options, 'hide-resize');
        }
        $this->replaceLayoutTokens([
            '{toolbarContainer}' => $this->renderToolbarContainer(),
            '{toolbar}' => $this->renderToolbar(),
            '{export}' => $this->renderExport(),
            '{toggleData}' =>  $this->renderToggleData(),
            '{items}' => Html::tag('div', '{items}', $this->containerOptions)
        ]);
        if (is_array($this->replaceTags) && !empty($this->replaceTags)) {
            foreach ($this->replaceTags as $key => $value) {
                if ($value instanceof \Closure) {
                    $value = call_user_func($value, $this);
                }
                $this->layout = str_replace($key, $value, $this->layout);
            }
        }
    }
    
    /**
     * Replace layout tokens
     * @param array $pairs the token to find and its replaced value as key value pairs
     */
    protected function replaceLayoutTokens($pairs)
    {
        foreach ($pairs as $token => $replace) {
            if (strpos($this->layout, $token) !== false) {
                $this->layout = str_replace($token, $replace, $this->layout);
            }
        }
    }

    /**
     * Begins the markup for the [[Pjax]] container.
     */
    protected function beginPjax()
    {
        if (!$this->pjax) {
            return;
        }
        $view = $this->getView();
        if (empty($this->pjaxSettings['options']['id'])) {
            $this->pjaxSettings['options']['id'] = $this->options['id'] . '-pjax';
        }
        $container = 'jQuery("#' . $this->pjaxSettings['options']['id'] . '")';
        $js = $container;
        if (ArrayHelper::getValue($this->pjaxSettings, 'neverTimeout', true)) {
            $js .= ".on('pjax:timeout', function(e){e.preventDefault()})";
        }
        $loadingCss = ArrayHelper::getValue($this->pjaxSettings, 'loadingCssClass', 'kv-grid-loading');
        $postPjaxJs = "setTimeout({$this->_gridClientFunc}, 2500);";
        $pjaxCont = '$("#' . $this->pjaxSettings['options']['id'] . '")';
        if ($loadingCss !== false) {
            if ($loadingCss === true) {
                $loadingCss = 'kv-grid-loading';
            }
            $js .= ".on('pjax:send', function(){{$pjaxCont}.addClass('{$loadingCss}')})";
            $postPjaxJs .= "{$pjaxCont}.removeClass('{$loadingCss}');";
        }
        $postPjaxJs .= "\n" . $this->_toggleScript;
        if (!empty($postPjaxJs)) {
            $event = 'pjax:complete.' . hash('crc32', $postPjaxJs);
            $js .= ".off('{$event}').on('{$event}', function(){{$postPjaxJs}})";
        }
        if ($js != $container) {
            $view->registerJs("{$js};");
        }
        Pjax::begin($this->pjaxSettings['options']);
        echo '<div class="kv-loader-overlay"><div class="kv-loader"></div></div>';
        echo ArrayHelper::getValue($this->pjaxSettings, 'beforeGrid', '');
    }

    /**
     * Ends the markup for the [[Pjax]] container.
     */
    protected function endPjax()
    {
        if (!$this->pjax) {
            return;
        }
        echo ArrayHelper::getValue($this->pjaxSettings, 'afterGrid', '');
        Pjax::end();
    }

    /**
     * Sets the grid panel layout based on the [[template]] and [[panel]] settings.
     */
    protected function renderPanel()
    {
        if (!$this->bootstrap || !is_array($this->panel) || empty($this->panel)) {
            return;
        }
        $type = ArrayHelper::getValue($this->panel, 'type', 'default');
        $heading = ArrayHelper::getValue($this->panel, 'heading', '');
        $footer = ArrayHelper::getValue($this->panel, 'footer', '');
        $before = ArrayHelper::getValue($this->panel, 'before', '');
        $after = ArrayHelper::getValue($this->panel, 'after', '');
        $headingOptions = ArrayHelper::getValue($this->panel, 'headingOptions', []);
        $footerOptions = ArrayHelper::getValue($this->panel, 'footerOptions', []);
        $beforeOptions = ArrayHelper::getValue($this->panel, 'beforeOptions', []);
        $afterOptions = ArrayHelper::getValue($this->panel, 'afterOptions', []);
        $panelHeading = '';
        $panelBefore = '';
        $panelAfter = '';
        $panelFooter = '';

        if ($heading !== false) {
            static::initCss($headingOptions, 'panel-heading');
            $content = strtr($this->panelHeadingTemplate, ['{heading}' => $heading]);
            $panelHeading = Html::tag('div', $content, $headingOptions);
        }
        if ($footer !== false) {
            static::initCss($footerOptions, 'panel-footer');
            $content = strtr($this->panelFooterTemplate, ['{footer}' => $footer]);
            $panelFooter = Html::tag('div', $content, $footerOptions);
        }
        if ($before !== false) {
            static::initCss($beforeOptions, 'kv-panel-before');
            $content = strtr($this->panelBeforeTemplate, ['{before}' => $before]);
            $panelBefore = Html::tag('div', $content, $beforeOptions);
        }
        if ($after !== false) {
            static::initCss($afterOptions, 'kv-panel-after');
            $content = strtr($this->panelAfterTemplate, ['{after}' => $after]);
            $panelAfter = Html::tag('div', $content, $afterOptions);
        }
        $this->layout = strtr(
            $this->panelTemplate,
            [
                '{panelHeading}' => $panelHeading,
                '{prefix}' => $this->panelPrefix,
                '{type}' => $type,
                '{panelFooter}' => $panelFooter,
                '{panelBefore}' => $panelBefore,
                '{panelAfter}' => $panelAfter,
            ]
        );
    }

    /**
     * Generates the toolbar.
     *
     * @return string
     */
    protected function renderToolbar()
    {
        if (empty($this->toolbar) || (!is_string($this->toolbar) && !is_array($this->toolbar))) {
            return '';
        }
        if (is_string($this->toolbar)) {
            return $this->toolbar;
        }
        $toolbar = '';
        foreach ($this->toolbar as $item) {
            if (is_array($item)) {
                $content = ArrayHelper::getValue($item, 'content', '');
                $options = ArrayHelper::getValue($item, 'options', []);
                static::initCss($options, 'btn-group');
                $toolbar .= Html::tag('div', $content, $options);
            } else {
                $toolbar .= "\n{$item}";
            }
        }
        return $toolbar;
    }

    /**
     * Generates the toolbar container with the toolbar
     */
    protected function renderToolbarContainer()
    {
        $tag = ArrayHelper::remove($this->toolbarContainerOptions, 'tag', 'div');
        return Html::tag($tag, $this->renderToolbar(), $this->toolbarContainerOptions);
    }

    /**
     * Generate HTML markup for additional table rows for header and/or footer.
     *
     * @param array|string $data the table rows configuration
     *
     * @return string
     */
    protected function generateRows($data)
    {
        if (empty($data)) {
            return '';
        }
        if (is_string($data)) {
            return $data;
        }
        $rows = '';
        if (is_array($data)) {
            foreach ($data as $row) {
                if (empty($row['columns'])) {
                    continue;
                }
                $rowOptions = ArrayHelper::getValue($row, 'options', []);
                $rows .= Html::beginTag('tr', $rowOptions);
                foreach ($row['columns'] as $col) {
                    $colOptions = ArrayHelper::getValue($col, 'options', []);
                    $colContent = ArrayHelper::getValue($col, 'content', '');
                    $tag = ArrayHelper::getValue($col, 'tag', 'th');
                    $rows .= "\t" . Html::tag($tag, $colContent, $colOptions) . "\n";
                }
                $rows .= Html::endTag('tr') . "\n";
            }
        }
        return $rows;
    }

    /**
     * Generate toggle data client validation script.
     */
    protected function genToggleDataScript()
    {
        $this->_toggleScript = '';
        if (!$this->toggleData) {
            return;
        }
        $minCount = ArrayHelper::getValue($this->toggleDataOptions, 'minCount', 0);
        if (!$minCount || $minCount >= $this->dataProvider->getTotalCount()) {
            return;
        }
        $view = $this->getView();
        $opts = Json::encode(
            [
                'id' => $this->_toggleButtonId,
                'pjax' => $this->pjax ? 1 : 0,
                'mode' => $this->_isShowAll ? 'all' : 'page',
                'msg' => ArrayHelper::getValue($this->toggleDataOptions, 'confirmMsg', ''),
                'lib' => new JsExpression(
                    ArrayHelper::getValue($this->krajeeDialogSettings, 'libName', 'krajeeDialog')
                ),
            ]
        );
        $this->_toggleOptionsVar = 'kvTogOpts_' . hash('crc32', $opts);
        $view->registerJs("{$this->_toggleOptionsVar}={$opts};", View::POS_HEAD);
        GridToggleDataAsset::register($view);
        $this->_toggleScript = "kvToggleData({$this->_toggleOptionsVar});";
    }

    /**
     * Registers client assets for the [[GridView]] widget.
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        $script = '';
        if ($this->bootstrap) {
            GridViewAsset::register($view);
        }
        Dialog::widget($this->krajeeDialogSettings);
        $gridId = $this->options['id'];
        $NS = '.' . str_replace('-', '_', $gridId);
        if ($this->export !== false && is_array($this->export) && !empty($this->export)) {
            GridExportAsset::register($view);
            $target = ArrayHelper::getValue($this->export, 'target', self::TARGET_BLANK);
            $gridOpts = Json::encode(
                [
                    'gridId' => $gridId,
                    'target' => $target,
                    'messages' => $this->export['messages'],
                    'exportConversions' => $this->exportConversions,
                    'showConfirmAlert' => ArrayHelper::getValue($this->export, 'showConfirmAlert', true),
                ]
            );
            $gridOptsVar = 'kvGridExp_' . hash('crc32', $gridOpts);
            $view->registerJs("var {$gridOptsVar}={$gridOpts};", View::POS_HEAD);
            foreach ($this->exportConfig as $format => $setting) {
                $id = "jQuery('#{$gridId} .export-{$format}')";
                $genOpts = Json::encode(
                    [
                        'filename' => $setting['filename'],
                        'showHeader' => $setting['showHeader'],
                        'showPageSummary' => $setting['showPageSummary'],
                        'showFooter' => $setting['showFooter'],
                    ]
                );
                $genOptsVar = 'kvGridExp_' . hash('crc32', $genOpts);
                $view->registerJs("var {$genOptsVar}={$genOpts};", View::POS_HEAD);
                $expOpts = Json::encode(
                    [
                        'dialogLib' => ArrayHelper::getValue($this->krajeeDialogSettings, 'libName', 'krajeeDialog'),
                        'gridOpts' => new JsExpression($gridOptsVar),
                        'genOpts' => new JsExpression($genOptsVar),
                        'alertMsg' => ArrayHelper::getValue($setting, 'alertMsg', false),
                        'config' => ArrayHelper::getValue($setting, 'config', []),
                    ]
                );
                $expOptsVar = 'kvGridExp_' . hash('crc32', $expOpts);
                $view->registerJs("var {$expOptsVar}={$expOpts};", View::POS_HEAD);
                $script .= "{$id}.gridexport({$expOptsVar});";
            }
        }
        $container = 'jQuery("#' . $this->containerOptions['id'] . '")';
        if ($this->resizableColumns) {
            $rcDefaults = [];
            if ($this->persistResize) {
                GridResizeStoreAsset::register($view);
            } else {
                $rcDefaults = ['store' => null];
            }
            $rcOptions = Json::encode(array_replace_recursive($rcDefaults, $this->resizableColumnsOptions));
            GridResizeColumnsAsset::register($view);
            $script .= "{$container}.resizableColumns('destroy').resizableColumns({$rcOptions});";
        }
        if ($this->floatHeader) {
            GridFloatHeadAsset::register($view);
            // fix floating header for IE browser when using group grid functionality
            $skipCss = '.kv-grid-group-row,.kv-group-header,.kv-group-footer'; // skip these CSS for IE
            $js = 'function($table){return $table.find("tbody tr:not(' . $skipCss . '):visible:first>*");}';
            $opts = [
                'floatTableClass' => 'kv-table-float',
                'floatContainerClass' => 'kv-thead-float',
                'getSizingRow' => new JsExpression($js),
            ];
            if ($this->floatOverflowContainer) {
                $opts['scrollContainer'] = new JsExpression("function(){return {$container};}");
            }
            $this->floatHeaderOptions = array_replace_recursive($opts, $this->floatHeaderOptions);
            $opts = Json::encode($this->floatHeaderOptions);
            $script .= "jQuery('#{$gridId} .kv-grid-table:first').floatThead({$opts});";
            // integrate resizeableColumns with floatThead
            if ($this->resizableColumns) {
                $script .= "{$container}.off('{$NS}').on('column:resize{$NS}', function(e){" .
                    'jQuery("#{$gridId} .kv-grid-table:nth-child(2)").floatThead("reflow");' .
                    '});';
            }
        }
        if ($this->perfectScrollbar) {
            GridPerfectScrollbarAsset::register($view);
            $script .= $container . '.perfectScrollbar(' . Json::encode($this->perfectScrollbarOptions) . ');';
        }
        $this->genToggleDataScript();
        $script .= $this->_toggleScript;
        $this->_gridClientFunc = 'kvGridInit_' . hash('crc32', $script);
        $this->options['data-krajee-grid'] = $this->_gridClientFunc;
        $view->registerJs("var {$this->_gridClientFunc}=function(){\n{$script}\n};\n{$this->_gridClientFunc}();");
    }
}
