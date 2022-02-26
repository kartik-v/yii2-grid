<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2022
 * @version   3.5.0
 */

namespace kartik\grid;

use Closure;
use Exception;
use kartik\base\Config;
use kartik\base\Lib;
use kartik\dialog\Dialog;
use Yii;
use yii\base\InvalidConfigException;
use yii\grid\Column;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\Request;
use yii\widgets\Pjax;

/**
 * The Krajee GridView Trait contains all methods and properties that enhances the Yii2 GridView widget.
 *
 * @see http://demos.krajee.com/grid
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since  1.0
 */
trait GridViewTrait
{
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
     * @var string the default label shown for each record in the grid (accusative case). This is applicable for few
     * languages like German.
     */
    public $itemLabelAccusative;

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
{panelHeading}
{panelBefore}
{items}
{panelAfter}
{panelFooter}
HTML;

    /**
     * @var string the template for rendering the panel heading. The following special tokens are
     * recognized and will be replaced:
     * - `{title}`: _string_, which will render the panel heading title content.
     * - `{summary}`: _string_, which will render the grid results summary.
     * - `{items}`: _string_, which will render the grid items.
     * - `{pager}`: _string_, which will render the grid pagination links.
     * - `{sort}`: _string_, which will render the grid sort links.
     * - `{toolbar}`: _string_, which will render the [[toolbar]] property passed
     * - `{toolbarContainer}`: _string_, which will render the toolbar container. See [[renderToolbarContainer()]].
     * - `{export}`: _string_, which will render the [[export]] menu button content.
     */
    public $panelHeadingTemplate = <<< HTML
    {summary}
    {title}
    <div class="clearfix"></div>
HTML;

    /**
     * @var string the template for rendering the panel footer. The following special tokens are
     * recognized and will be replaced:
     * - `{title}`: _string_, which will render the panel heading title content.
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
    public $panelPrefix;

    /**
     * @var array the panel settings for displaying the grid view within a bootstrap styled panel. This property is
     * therefore applicable only if [[bootstrap]] property is `true`. The following array keys can be configured:
     * - `type`: _string_, the panel contextual type. Set it to one of the TYPE constants. If not set, will default to
     *   [[TYPE_DEFAULT]].
     * - `options`: _array_, the HTML attributes for the panel container. If the `class` is not set, it will be auto
     *   derived using the panel `type` and [[panelPrefix]]
     * - `heading`: `string`|`boolean`, the panel heading. If set to `false`, will not be displayed.
     * - `headingOptions`: _array_, HTML attributes for the panel heading container. Defaults to:
     *   - `['class'=>'panel-heading']` when [[bsVersion]] = `3.x`, and
     *   - `['class'=>'card-heading <COLOR>']` when [[bsVersion]] = `4.x` - the color will be auto calculated based on
     *      the `type` setting
     * - `titleOptions`: _array_, HTML attributes for the panel title container. The following tags are specially
     *   parsed:
     *   - `tag`: _string_, the HTML tag to render the title. Defaults to `h3` when [[bsVersion]] = `3.x` and `span`
     *     when [[bsVersion]] = `4.x`
     *   The `titleOptions` defaults to:
     *   - `['class'=>'panel-title']` when [[bsVersion]] = `3.x`, and
     *   - `[]` when [[bsVersion]] = `4.x`
     * - `summaryOptions`: _array_, HTML attributes for the panel summary section container. Defaults to:
     *   - `['class'=>'pull-right']` when [[bsVersion]] = `3.x`, and
     *   - `['class'=>'float-right']` when [[bsVersion]] = `4.x`, and
     * - `footer`: `string`|`boolean`, the panel footer. If set to `false` will not be displayed.
     * - `footerOptions`: _array_, HTML attributes for the panel footer container. Defaults to:
     *   - `['class'=>'panel-footer']` when [[bsVersion]] = `3.x`, and
     *   - `['class'=>'card-footer']` when [[bsVersion]] = `4.x`
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
    public $toolbarContainerOptions = ['class' => 'btn-toolbar kv-grid-toolbar toolbar-container'];

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
     * @var bool whether to enable focused edited row feature
     */
    public $enableEditedRow = false;

    /**
     * @var array the configuration for the row being currently edited
     */
    public $editedRowConfig = [
        'rowIdGetParam' => 'row',
        'gridIdGetParam' => 'grid',
        'gridFiltersSessionParam' => 'kvGridFiltersCache',
        'highlightClass' => 'kv-row-edit-highlight',
    ];

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
     * Note that if you set this to `true` and `floatHeader` or `floatFooter` or `floatPageSummary` is also
     * enabled to `true` - then for effective behavior set a fixed height for the container in `containerOptions`
     * or add the built in class `kv-grid-wrapper` to the `containerOptions` - for example:
     * ```
     *     'containerOptions' => ['class' => 'kv-grid-wrapper']
     * ```
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
     * @var boolean whether the grid will have a floating table header. Note that the table header will stick to the
     * top of the page by default if this is set to `true`. To add an offset - you can configure the CSS style
     * within `headerContainer` - for example:
     *
     * ```
     *    'headerContainer' => ['class' => 'kv-table-header, 'style' => 'top: 50px'] // to set an offset
     * ```
     */
    public $floatHeader = false;

    /**
     * @var boolean whether the grid will have a floating table footer.
     */
    public $floatFooter = false;

    /**
     * @var boolean whether the grid table will have a floating page summary at the bottom or top depending on
     * `pageSummaryPosition`.  Defaults to `false`. Note that this property also automatically overrides and disables
     * the `floatHeader` or `floatFooter` properties. This is because only one sticky container can exist at the top
     * or bottom. Note:
     * - when `pageSummaryPosition` is set to `GridView::POS_BOTTOM`, the page summary sticks to the bottom of the page,
     *   and overrides the `floatFooter` setting to `false`.
     * - when `pageSummaryPosition` is set to `GridView::POS_TOP`, the page summary sticks to the top of the page,
     *   and overrides the `floatHeader` setting to `false`.
     * Note that, like header or footer, you can control the positioning or offset of the page summary container via
     * `pageSummaryContainer`.
     *
     * ```
     *    'pageSummaryContainer' => ['style' => 'top: 50px'] // to set an offset
     * ```
     */
    public $floatPageSummary = false;

    /**
     * @var array the HTML options for the table `thead`. The CSS class 'kv-table-header' is added by default and
     * creates the Krajee default header styling for a better float header behavior. In case you are overriding this
     * property at runtime, either use your own CSS class/ style or add the default CSS 'kv-table-header'. Note that
     * with `floatHeader` enabled to `true`, you may need to add an offset for the floated header from top when
     * scrolling (e.g. in cases where you have a fixed bootstrap navbar on top). For example:
     *
     * ```
     *    'headerContainer' => ['class' => 'kv-table-header, 'style' => 'top: 50px'] // to set an offset
     * ```
     */
    public $headerContainer = ['class' => 'kv-table-header'];

    /**
     * @var array the HTML options for the table <code>tfoot</code> container. The CSS class 'kv-table-footer' is added
     * by default, and creates the Krajee default footer styling for a better float footer behavior. In case you are
     * overriding this property at runtime, either use your own CSS class/ style or add the default CSS
     * 'kv-table-footer' for maintaining a consistent sticky styling. Similar, to `headerContainer`, you can control
     * other styling, like offsets. For example:
     *
     * ```
     * 'footerContainer' => ['class' => 'kv-table-footer, 'style' => 'bottom: 50px'] // to set an offset from bottom
     * ```
     */
    public $footerContainer = ['class' => 'kv-table-footer'];

    /**
     * @deprecated since release v3.5.0
     */
    public $floatOverflowContainer = false;

    /**
     * @deprecated since release v3.5.0
     */
    public $floatHeaderOptions = [];

    /**
     * @var boolean whether pretty perfect scrollbars using perfect scrollbar plugin is to be used. Defaults to
     * `false`.
     *
     * @see https://github.com/utatti/perfect-scrollbar
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
     * @var string location of the page summary row (whether [[POS_TOP]] or [[POS_BOTTOM]])
     */
    public $pageSummaryPosition = self::POS_BOTTOM;

    /**
     * @array the HTML attributes for the page summary container. The following special options are recognized:
     *
     * - `tag`: _string_, the tag used to render the page summary. Defaults to `tbody`.
     */
    public $pageSummaryContainer = ['class' => 'kv-page-summary-container'];

    /**
     * @array the HTML attributes for the summary row.
     */
    public $pageSummaryRowOptions = [];

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
     *              'class' => 'btn btn-default', // 'btn btn-secondary' for BS4.x / BS5.x
     *              'title' => 'Show all data'
     *          ],
     *          'page' => [
     *              'icon' => 'resize-small',
     *              'label' => 'Page',
     *              'class' => 'btn btn-default', // 'btn btn-secondary' for BS4.x / BS5.x
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
     *    - `['class' => 'btn btn-default']` for [[bsVersion]] = '3.x' or .
     *    - `['class' => 'btn btn-secondary']` for [[bsVersion]] = '4.x' / '5.x'
     * - `encoding`: _string_, the export output file encoding. If not set, defaults to `utf-8`.
     * - `bom`: `boolean`, whether a BOM is to be embedded for text or CSV files with utf-8 encoding. Defaults to
     *   `true`.
     * - `menuOptions`: _array_, HTML attributes for the export dropdown menu. Defaults to `['class' => 'dropdown-menu
     *   dropdown-menu-right']`. This property is to be setup exactly as the `options` property required by the
     *   [[\yii\bootstrap\Dropdown]] widget.
     * - `skipExportElements`: _array_, the list of jQuery element selectors that will be skipped and removed from
     *   export. Defaults to `['.sr-only', '.hide']`.
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
     *            `https://maxcdn.bootstrapcdn.com/bootstrap/3.5.0/css/bootstrap.min.css`.
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
     * Whether to hash export config and prevent data tampering of the export config when transmitting this between
     * client and server during grid data export. Defaults to `true`. You may set this to `false` if your config
     * contains dynamic data (like current date time). However, note that when `false` it adds the possibility of
     * your client data being tampered during grid export when read by server.
     */
    public $hashExportConfig = true;

    /**
     * @var array the configuration for sorter icons. The array key must have an `SORT_ASC` and `SORT_DESC` entry.
     * The `sorterIcons` property defaults to following if not overridden:
     *
     * For Bootstrap v4.x and v5.x:
     * [
     *   SORT_ASC => '<i class="fas fa-sort-amount-down-alt"></i>',
     *   SORT_DESC => '<i class="fas fa-sort-amount-up"></i>'
     * ]
     *
     * For Bootstrap v3.x:
     * [
     *   SORT_ASC => '<i class="glyphicon glyphicon-sort-by-attributes"></i>',
     *   SORT_DESC => '<i class="glyphicon glyphicon-sort-by-attributes-alt"></i>',
     * ]
     */
    public $sorterIcons = [];

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
     * @var string|null the filter cache
     */
    protected $_filterCache = null;

    /**
     * Initializes the Krajee GridView widget
     * @throws InvalidConfigException
     */
    protected function initGridView()
    {
        $this->initModule();
        if (isset($this->_module->bsVersion)) {
            $this->bsVersion = $this->_module->bsVersion;
        }
        $this->initBsVersion();
        $bsVer = $this->getBsVer();
        $notBs3 = $bsVer > 3;
        Html::addCssClass($this->options, 'is-bs'.($notBs3 ? '4' : '3'));
        $this->sorterIcons += static::getDefaultSorterIcons($notBs3);
        $this->initPjaxContainerId();
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
        if (!isset($this->itemLabelAccusative)) {
            $this->itemLabelAccusative = Yii::t('kvgrid', 'items-acc');
        }
        if ($notBs3) {
            Html::addCssClass($this->options, 'kv-grid-bs4');
            $this->setPagerOptionClass('linkContainerOptions', 'page-item');
            $this->setPagerOptionClass('linkOptions', 'page-link');
            $this->setPagerOptionClass('disabledListItemSubTagOptions', 'page-link');
        } else {
            Html::addCssClass($this->options, 'kv-grid-bs3');
        }
        if (empty($this->sorter['class'])) {
            $this->sorter['class'] = GridLinkSorter::class;
            $this->sorter['sorterIcons'] = $this->sorterIcons;
        }
        if (!$this->toggleData) {
            return;
        }
        $this->_toggleDataKey = '_tog'.hash('crc32', $this->options['id']);
        /**
         * @var Request $request
         */
        $request = $this->_module->get('request', false);
        if ($request === null || !($request instanceof Request)) {
            $request = Yii::$app->request;
        }
        $this->_isShowAll = $request->getQueryParam($this->_toggleDataKey, $this->defaultPagination) === 'all';
        if ($this->_isShowAll) {
            $this->dataProvider->pagination = false;
        }
        $this->_toggleButtonId = $this->options['id'].'-togdata-'.($this->_isShowAll ? 'all' : 'page');
    }

    /**
     * Prepares the Krajee GridView widget for run
     * @throws InvalidConfigException
     */
    protected function prepareGridView()
    {
        $this->initToggleData();
        $this->initExport();
        if ($this->export !== false && isset($this->exportConfig[self::PDF])) {
            Config::checkDependency(
                'mpdf\Pdf',
                'yii2-mpdf',
                'for PDF export functionality. To include PDF export, follow the install steps below. If you do not '.
                "need PDF export functionality, do not include 'PDF' as a format in the 'export' property. You can ".
                "otherwise set 'export' to 'false' to disable all export functionality"
            );
        }
        $this->initBootstrapStyle();
        $this->containerOptions['id'] = $this->options['id'].'-container';
        Html::addCssClass($this->containerOptions, 'kv-grid-container');
        $this->initPanel();
        $this->initLayout();
        $this->registerAssets();
    }

    /**
     * Gets default sorter icons
     * @param  bool  $notBs3
     * @return array
     */
    public static function getDefaultSorterIcons($notBs3)
    {
        if ($notBs3) {
            return [
                SORT_ASC => '<i class="fas fa-sort-amount-down-alt"></i>',
                SORT_DESC => '<i class="fas fa-sort-amount-up"></i>',
            ];
        }

        return [
            SORT_ASC => '<i class="glyphicon glyphicon-sort-by-attributes"></i>',
            SORT_DESC => '<i class="glyphicon glyphicon-sort-by-attributes-alt"></i>',
        ];
    }

    /**
     * Parses export configuration and returns the merged defaults.
     *
     * @param  array  $exportConfig  the export configuration
     * @param  array  $defaultExportConfig  the default export configuration
     *
     * @return array
     */
    protected static function parseExportConfig($exportConfig, $defaultExportConfig)
    {
        if (is_array($exportConfig) && !empty($exportConfig)) {
            foreach ($exportConfig as $format => $setting) {
                $setup = is_array($setting) ? $setting : [];
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
     * @param  array  $options  the HTML options
     * @param  string|array  $css  the CSS class to test and append
     */
    protected static function initCss(&$options, $css)
    {
        if (!isset($options['class'])) {
            $options['class'] = $css;
        }
    }

    /**
     * Get pjax container identifier
     * @return string
     */
    public function getPjaxContainerId()
    {
        $this->initPjaxContainerId();

        return $this->pjaxSettings['options']['id'];
    }

    /**
     * Initializes pjax container identifier
     */
    public function initPjaxContainerId()
    {
        if (empty($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        if (empty($this->pjaxSettings['options']['id'])) {
            $this->pjaxSettings['options']['id'] = $this->options['id'].'-pjax';
        }
    }

    /**
     * @return array|false
     */
    protected function initEditedRow()
    {
        if (!$this->enableEditedRow) {
            return false;
        }
        $cfg = $this->editedRowConfig;
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $row = !isset($cfg['rowIdGetParam']) ? null : $request->get($cfg['rowIdGetParam']);
        $grid = !isset($cfg['gridIdGetParam']) ? null : $request->get($cfg['gridIdGetParam'], $this->options['id']);
        if (!empty($session) && !empty($cfg['gridFiltersSessionParam'])) {
            $filter = $cfg['gridFiltersSessionParam'];
            $queryParams = $request->queryParams;
            unset($queryParams[$cfg['rowIdGetParam']], $queryParams[$cfg['gridIdGetParam']]);
            $session->set($filter, Json::encode($queryParams));
        }

        return ['row' => $row, 'grid' => $grid, 'css' => $cfg['highlightClass']];
    }

    /**
     * Adds CSS class to the pager parameter
     * @param  string  $param  the pager param
     * @param  string  $css  the CSS class
     * @throws Exception
     */
    protected function setPagerOptionClass($param, $css)
    {
        $opts = ArrayHelper::getValue($this->pager, $param, []);
        Html::addCssClass($opts, $css);
        $this->pager[$param] = $opts;
    }

    /**
     * Renders the table page summary.
     *
     * @return string the rendering result.
     * @throws Exception
     */
    public function renderPageSummary()
    {
        if (!$this->showPageSummary) {
            return null;
        }
        $notBs3 = !$this->isBs(3);
        if (!isset($this->pageSummaryRowOptions['class'])) {
            $this->pageSummaryRowOptions['class'] = ($notBs3 ? 'table-' : '').'warning kv-page-summary';
        }
        Html::addCssClass($this->pageSummaryRowOptions, $this->options['id']);
        $row = $this->getPageSummaryRow();
        if ($row === null) {
            return '';
        }
        $tag = ArrayHelper::remove($this->pageSummaryContainer, 'tag', 'tbody');
        $content = Html::tag('tr', $row, $this->pageSummaryRowOptions);

        return Html::tag($tag, $content, $this->pageSummaryContainer);
    }

    /**
     * Get the page summary row markup
     * @return string
     * @throws Exception
     */
    protected function getPageSummaryRow()
    {
        $columns = array_values($this->columns);
        $cols = count($columns);
        if ($cols === 0) {
            return null;
        }
        $cells = [];
        $skipped = [];
        for ($i = 0; $i < $cols; $i++) {
            /** @var DataColumn $column */
            $column = $columns[$i];
            if (!method_exists($column, 'renderPageSummaryCell')) {
                $cells[] = Html::tag('td');
                continue;
            }
            $cells[] = $column->renderPageSummaryCell();
            if (!empty($column->pageSummaryOptions['colspan'])) {
                $span = (int)$column->pageSummaryOptions['colspan'];
                $dir = ArrayHelper::getValue($column->pageSummaryOptions, 'data-colspan-dir', 'ltr');
                if ($span > 0) {
                    $fm = ($dir === 'ltr') ? ($i + 1) : ($i - $span + 1);
                    $to = ($dir === 'ltr') ? ($i + $span - 1) : ($i - 1);
                    for ($j = $fm; $j <= $to; $j++) {
                        $skipped[$j] = true;
                    }
                }
            }
        }
        if (!empty($skipped)) {
            for ($i = 0; $i < $cols; $i++) {
                if (isset($skipped[$i])) {
                    $cells[$i] = '';
                }
            }
        }

        return implode('', $cells);
    }

    /**
     * Renders a table row with the given data model and key.
     * @param  mixed  $model  the data model to be rendered
     * @param  mixed  $key  the key associated with the data model
     * @param  int  $index  the zero-based index of the data model among the model array returned by [[dataProvider]].
     * @return string the rendering result
     */
    public function renderTableRow($model, $key, $index)
    {
        $cells = [];
        /* @var $column Column */
        foreach ($this->columns as $column) {
            $cells[] = $column->renderDataCell($model, $key, $index);
        }
        if ($this->rowOptions instanceof Closure) {
            $options = call_user_func($this->rowOptions, $model, $key, $index, $this);
        } else {
            $options = $this->rowOptions;
        }
        $options['data-key'] = static::parseKey($key);
        Html::addCssClass($options, $this->options['id']);

        return Html::tag('tr', implode('', $cells), $options);
    }

    /**
     * Parses the key and returns parsed key value as string based on the data type
     * @param  mixed  $key
     * @return string
     */
    public static function parseKey($key)
    {
        return is_array($key) ? Json::encode($key) : (is_object($key) ? serialize($key) : (string)$key);
    }

    /**
     * Renders the toggle data button.
     *
     * @return string
     * @throws Exception
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
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function renderExport()
    {
        if ($this->export === false || !is_array($this->export) ||
            empty($this->exportConfig) || !is_array($this->exportConfig)
        ) {
            return '';
        }
        $bsVer = $this->getBsVer();
        $notBs3 = $bsVer > 3;
        $title = $this->export['label'];
        $icon = $this->export['icon'];
        $options = $this->export['options'];
        static::initCss($options, ['btn', $this->getDefaultBtnCss()]);
        $menuOptions = $this->export['menuOptions'];
        $title = ($icon == '') ? $title : "<i class='{$icon}'></i> {$title}";
        $encoding = ArrayHelper::getValue($this->export, 'encoding', 'utf-8');
        $bom = (int)ArrayHelper::getValue($this->export, 'bom', 1);
        $items = empty($this->export['header']) ? [] : [$this->export['header']];
        foreach ($this->exportConfig as $format => $setting) {
            $iconOptions = ArrayHelper::getValue($setting, 'iconOptions', []);
            Html::addCssClass($iconOptions, $setting['icon']);
            $label = (empty($setting['icon']) || $setting['icon'] == '') ? $setting['label'] :
                Html::tag('i', '', $iconOptions).' '.$setting['label'];
            $mime = ArrayHelper::getValue($setting, 'mime', 'text/plain');
            $config = ArrayHelper::getValue($setting, 'config', []);
            $cssStyles = ArrayHelper::getValue($setting, 'cssStyles', []);
            if ($format === self::JSON) {
                unset($config['jsonReplacer']);
            }
            $cfg = $this->hashExportConfig ? Json::encode($config) : '';
            $intCfg = empty($this->hashExportConfig) ? 0 : 1;
            $dataToHash = $this->moduleId.$setting['filename'].$mime.$encoding.$bom.$intCfg.$cfg;
            $hash = Yii::$app->security->hashData($dataToHash, $this->_module->exportEncryptSalt);
            $items[] = [
                'label' => $label,
                'url' => '#',
                'linkOptions' => [
                    'class' => 'export-'.$format,
                    'data-mime' => $mime,
                    'data-hash' => $hash,
                    'data-hash-export-config' => $intCfg,
                    'data-css-styles' => $cssStyles,
                ],
                'options' => $setting['options'],
            ];
        }
        $itemsBefore = ArrayHelper::getValue($this->export, 'itemsBefore', []);
        $itemsAfter = ArrayHelper::getValue($this->export, 'itemsAfter', []);
        $items = ArrayHelper::merge($itemsBefore, $items, $itemsAfter);
        $opts = [
            'label' => $title,
            'dropdown' => ['items' => $items, 'encodeLabels' => false, 'options' => $menuOptions],
            'encodeLabel' => false,
        ];
        Html::addCssClass($this->exportContainer, 'btn-group');
        $dropdown = $this->getDropdownClass(true);
        if ($notBs3) {
            $opts['buttonOptions'] = $options;
            $opts['renderContainer'] = false;
            /** @noinspection PhpUndefinedMethodInspection */
            $out = Html::tag('div', $dropdown::widget($opts), $this->exportContainer);
        } else {
            $opts['options'] = $options;
            $opts['containerOptions'] = $this->exportContainer;
            /** @noinspection PhpUndefinedMethodInspection */
            $out = $dropdown::widget($opts);
        }

        return $out;
    }

    /**
     * Initialize the module based on module identifier
     * @throws InvalidConfigException
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
        $this->_module = Config::getModule($this->moduleId, Module::class);
    }

    /**
     * Initialize grid export.
     * @throws Exception
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
        $bsVer = $this->getBsVer();
        $notBs3 = $bsVer > 3;
        $this->export = array_replace_recursive(
            [
                'label' => '',
                'icon' => $isFa ? 'fa fa-share-square-o' : ($notBs3 ? 'fas fa-external-link-alt' : 'glyphicon glyphicon-export'),
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
                'options' => ['class' => 'btn '.$this->getDefaultBtnCss(), 'title' => Yii::t('kvgrid', 'Export')],
                'menuOptions' => ['class' => 'dropdown-menu dropdown-menu-right '],
                'skipExportElements' => ['.sr-only', '.hide'],
            ],
            $this->export
        );
        if (!isset($this->export['header'])) {
            $this->export['header'] = '<li role="presentation" class="dropdown-header">'.
                Yii::t('kvgrid', 'Export Page Data').'</li>';
        }
        if (!isset($this->export['headerAll'])) {
            $this->export['headerAll'] = '<li role="presentation" class="dropdown-header">'.
                Yii::t('kvgrid', 'Export All Data').'</li>';
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
                'content' => Yii::t('kvgrid', 'Generated').': '.date('D, d-M-Y'),
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
        $cssStyles = [
            '.kv-group-even' => ['background-color' => '#f0f1ff'],
            '.kv-group-odd' => ['background-color' => '#f9fcff'],
            '.kv-grouped-row' => ['background-color' => '#fff0f5', 'font-size' => '1.3em', 'padding' => '10px'],
            '.kv-table-caption' => [
                'border' => '1px solid #ddd',
                'border-bottom' => 'none',
                'font-size' => '1.5em',
                'padding' => '8px',
            ],
            '.kv-table-footer' => ['border-top' => '4px double #ddd', 'font-weight' => 'bold'],
            '.kv-page-summary td' => [
                'background-color' => '#ffeeba',
                'border-top' => '4px double #ddd',
                'font-weight' => 'bold',
            ],
            '.kv-align-center' => ['text-align' => 'center'],
            '.kv-align-left' => ['text-align' => 'left'],
            '.kv-align-right' => ['text-align' => 'right'],
            '.kv-align-top' => ['vertical-align' => 'top'],
            '.kv-align-bottom' => ['vertical-align' => 'bottom'],
            '.kv-align-middle' => ['vertical-align' => 'middle'],
            '.kv-editable-link' => [
                'color' => '#428bca',
                'text-decoration' => 'none',
                'background' => 'none',
                'border' => 'none',
                'border-bottom' => '1px dashed',
                'margin' => '0',
                'padding' => '2px 1px',
            ],
        ];

        $ver = $bsVer === 5 ? '5.1.0' : ($bsVer === 4 ? '4.6.0' : '3.4.1');
        $cssFile = ["https://cdn.jsdelivr.net/npm/bootstrap@{$ver}/dist/css/bootstrap.min.css"];
        if ($notBs3) {
            $cssFile[] = 'https://use.fontawesome.com/releases/v5.3.1/css/all.css';
        }
        $defaultExportConfig = [
            self::HTML => [
                'label' => Yii::t('kvgrid', 'HTML'),
                'icon' => $notBs3 ? 'fas fa-file-alt' : ($isFa ? 'fa fa-file-text' : 'glyphicon glyphicon-save'),
                'iconOptions' => ['class' => 'text-info'],
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => Yii::t('kvgrid', 'grid-export'),
                'alertMsg' => Yii::t('kvgrid', 'The HTML export file will be generated for download.'),
                'options' => ['title' => Yii::t('kvgrid', 'Hyper Text Markup Language')],
                'mime' => 'text/plain',
                'cssStyles' => $cssStyles,
                'config' => [
                    'cssFile' => $cssFile,
                ],
            ],
            self::CSV => [
                'label' => Yii::t('kvgrid', 'CSV'),
                'icon' => $notBs3 ? 'fas fa-file-code' : ($isFa ? 'fa fa-file-code-o' : 'glyphicon glyphicon-floppy-open'),
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
                'icon' => $notBs3 ? 'far fa-file-alt' : ($isFa ? 'fa fa-file-text-o' : 'glyphicon glyphicon-floppy-save'),
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
                'icon' => $notBs3 ? 'far fa-file-excel' : ($isFa ? 'fa fa-file-excel-o' : 'glyphicon glyphicon-floppy-remove'),
                'iconOptions' => ['class' => 'text-success'],
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => Yii::t('kvgrid', 'grid-export'),
                'alertMsg' => Yii::t('kvgrid', 'The EXCEL export file will be generated for download.'),
                'options' => ['title' => Yii::t('kvgrid', 'Microsoft Excel 95+')],
                'mime' => 'application/vnd.ms-excel',
                'cssStyles' => $cssStyles,
                'config' => [
                    'worksheet' => Yii::t('kvgrid', 'ExportWorksheet'),
                    'cssFile' => '',
                ],
            ],
            self::PDF => [
                'label' => Yii::t('kvgrid', 'PDF'),
                'icon' => $notBs3 ? 'far fa-file-pdf' : ($isFa ? 'fa fa-file-pdf-o' : 'glyphicon glyphicon-floppy-disk'),
                'iconOptions' => ['class' => 'text-danger'],
                'showHeader' => true,
                'showPageSummary' => true,
                'showFooter' => true,
                'showCaption' => true,
                'filename' => Yii::t('kvgrid', 'grid-export'),
                'alertMsg' => Yii::t('kvgrid', 'The PDF export file will be generated for download.'),
                'options' => ['title' => Yii::t('kvgrid', 'Portable Document Format')],
                'mime' => 'application/pdf',
                'cssStyles' => $cssStyles,
                'config' => [
                    'mode' => 'UTF-8',
                    'format' => 'A4-L',
                    'destination' => 'D',
                    'marginTop' => 20,
                    'marginBottom' => 20,
                    'cssInline' => '.kv-wrap{padding:20px}',
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
                'icon' => $notBs3 ? 'far fa-file-code' : ($isFa ? 'fa fa-file-code-o' : 'glyphicon glyphicon-floppy-open'),
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
     * @throws Exception
     */
    protected function initToggleData()
    {
        if (!$this->toggleData) {
            return;
        }
        $notBs3 = !$this->isBs(3);
        $defBtnCss = 'btn '.$this->getDefaultBtnCss();
        $defaultOptions = [
            'maxCount' => 10000,
            'minCount' => 500,
            'confirmMsg' => Yii::t(
                'kvgrid',
                'There are {totalCount} records. Are you sure you want to display them all?',
                ['totalCount' => number_format($this->dataProvider->getTotalCount())]
            ),
            'all' => [
                'icon' => $notBs3 ? 'fas fa-expand' : 'glyphicon glyphicon-resize-full',
                'label' => Yii::t('kvgrid', 'All'),
                'class' => $defBtnCss,
                'title' => Yii::t('kvgrid', 'Show all data'),
            ],
            'page' => [
                'icon' => $notBs3 ? 'fas fa-compress' : 'glyphicon glyphicon-resize-small',
                'label' => Yii::t('kvgrid', 'Page'),
                'class' => $defBtnCss,
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
            $label = "<i class='{$icon}'></i> ".$label;
        }
        $this->toggleDataOptions[$tag]['label'] = $label;
        if (!isset($this->toggleDataOptions[$tag]['title'])) {
            $this->toggleDataOptions[$tag]['title'] = $defaultOptions[$tag]['title'];
        }
        $this->toggleDataOptions[$tag]['data-pjax'] = $this->pjax ? 'true' : false;
    }

    /**
     * Initialize bootstrap specific styling.
     * @throws Exception
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
            $this->addCssClass($this->tableOptions, self::BS_TABLE_CONDENSED);
        }
        if ($this->floatPageSummary) {
            if ($this->pageSummaryPosition === self::POS_BOTTOM) {
                $this->floatFooter = false;
                $css = 'kv-float-footer';
            } else {
                $this->floatHeader = false;
                $css = 'kv-float-header';
            }
            Html::addCssClass($this->pageSummaryContainer, $css);
        }
        Html::addCssClass($this->headerContainer, [$this->options['id']]);
        if ($this->floatHeader) {
            Html::addCssClass($this->headerContainer, 'kv-float-header');
        }
        if ($this->floatFooter) {
            Html::addCssClass($this->footerContainer, 'kv-float-footer');
        }
        if ($this->responsive) {
            Html::addCssClass($this->containerOptions, 'table-responsive');
        }
        if ($this->responsiveWrap) {
            Html::addCssClass($this->tableOptions, 'kv-table-wrap');
        }
    }

    /**
     * Initialize the grid layout.
     * @throws InvalidConfigException
     */
    protected function initLayout()
    {
        Html::addCssClass($this->filterRowOptions, ['filters', 'skip-export']);
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
            '{toggleData}' => $this->renderToggleData(),
            '{items}' => Html::tag('div', '{items}', $this->containerOptions),
        ]);
        if (is_array($this->replaceTags) && !empty($this->replaceTags)) {
            foreach ($this->replaceTags as $key => $value) {
                if ($value instanceof Closure) {
                    $value = call_user_func($value, $this);
                }
                $this->layout = Lib::str_replace($key, $value, $this->layout);
            }
        }
    }

    /**
     * Replace layout tokens
     * @param  array  $pairs  the token to find and its replaced value as key value pairs
     */
    protected function replaceLayoutTokens($pairs)
    {
        foreach ($pairs as $token => $replace) {
            if (Lib::strpos($this->layout, $token) !== false) {
                $this->layout = Lib::str_replace($token, $replace, $this->layout);
            }
        }
    }

    /**
     * Begins the pjax widget rendering
     */
    protected function beginPjax()
    {
        $view = $this->getView();
        $container = 'jQuery("#'.$this->pjaxSettings['options']['id'].'")';
        $js = $container;
        if (ArrayHelper::getValue($this->pjaxSettings, 'neverTimeout', true)) {
            $js .= ".on('pjax:timeout', function(e){e.preventDefault()})";
        }
        $loadingCss = ArrayHelper::getValue($this->pjaxSettings, 'loadingCssClass', 'kv-grid-loading');
        $postPjaxJs = "setTimeout({$this->_gridClientFunc}, 2500);";
        $pjaxCont = '$("#'.$this->pjaxSettings['options']['id'].'")';
        if ($loadingCss !== false) {
            if ($loadingCss === true) {
                $loadingCss = 'kv-grid-loading';
            }
            $js .= ".on('pjax:send', function(){{$pjaxCont}.addClass('{$loadingCss}')})";
            $postPjaxJs .= "{$pjaxCont}.removeClass('{$loadingCss}');";
        }
        $postPjaxJs .= "\n".$this->_toggleScript;
        if (!empty($postPjaxJs)) {
            $event = 'pjax:complete.'.hash('crc32', $postPjaxJs);
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
     * Completes the pjax widget rendering
     */
    protected function endPjax()
    {
        echo ArrayHelper::getValue($this->pjaxSettings, 'afterGrid', '');
        Pjax::end();
    }

    /**
     * Initializes and sets the grid panel layout based on the [[template]] and [[panel]] settings.
     * @throws Exception
     */
    protected function initPanel()
    {
        if (!$this->bootstrap || !is_array($this->panel) || empty($this->panel)) {
            return;
        }
        $options = ArrayHelper::getValue($this->panel, 'options', []);
        $type = ArrayHelper::getValue($this->panel, 'type', 'default');
        $heading = ArrayHelper::getValue($this->panel, 'heading', '');
        $footer = ArrayHelper::getValue($this->panel, 'footer', '');
        $before = ArrayHelper::getValue($this->panel, 'before', '');
        $after = ArrayHelper::getValue($this->panel, 'after', '');
        $headingOptions = ArrayHelper::getValue($this->panel, 'headingOptions', []);
        $titleOptions = ArrayHelper::getValue($this->panel, 'titleOptions', []);
        $footerOptions = ArrayHelper::getValue($this->panel, 'footerOptions', []);
        $beforeOptions = ArrayHelper::getValue($this->panel, 'beforeOptions', []);
        $afterOptions = ArrayHelper::getValue($this->panel, 'afterOptions', []);
        $summaryOptions = ArrayHelper::getValue($this->panel, 'summaryOptions', []);
        $panelHeading = '';
        $panelBefore = '';
        $panelAfter = '';
        $panelFooter = '';
        $notBs3 = !$this->isBs(3);
        if (isset($this->panelPrefix)) {
            static::initCss($options, $this->panelPrefix.$type);
        } else {
            $this->addCssClass($options, self::BS_PANEL);
            $border = $type === self::TYPE_LIGHT ? 'border' : "border-{$type}";
            Html::addCssClass($options, $notBs3 ? $border : "panel-{$type}");
        }
        static::initCss($summaryOptions, $this->getCssClass(self::BS_PULL_RIGHT));
        $titleTag = ArrayHelper::remove($titleOptions, 'tag', ($notBs3 ? 'h5' : 'h3'));
        static::initCss($titleOptions, $notBs3 ? 'm-0' : $this->getCssClass(self::BS_PANEL_TITLE));
        if ($heading !== false) {
            $color = ' '.$this->getCssClass('panel-'.$type);
            static::initCss($headingOptions, $this->getCssClass(self::BS_PANEL_HEADING).$color);
            $panelHeading = Html::tag('div', $this->panelHeadingTemplate, $headingOptions);
        }
        if ($footer !== false) {
            static::initCss($footerOptions, $this->getCssClass(self::BS_PANEL_FOOTER));
            $content = Lib::strtr($this->panelFooterTemplate, ['{footer}' => $footer]);
            $panelFooter = Html::tag('div', $content, $footerOptions);
        }
        if ($before !== false) {
            static::initCss($beforeOptions, 'kv-panel-before');
            $content = Lib::strtr($this->panelBeforeTemplate, ['{before}' => $before]);
            $panelBefore = Html::tag('div', $content, $beforeOptions);
        }
        if ($after !== false) {
            static::initCss($afterOptions, 'kv-panel-after');
            $content = Lib::strtr($this->panelAfterTemplate, ['{after}' => $after]);
            $panelAfter = Html::tag('div', $content, $afterOptions);
        }
        $out = Lib::strtr($this->panelTemplate, [
            '{panelHeading}' => $panelHeading,
            '{type}' => $type,
            '{panelFooter}' => $panelFooter,
            '{panelBefore}' => $panelBefore,
            '{panelAfter}' => $panelAfter,
        ]);

        $this->layout = Html::tag('div', Lib::strtr($out, [
            '{title}' => Html::tag($titleTag, $heading, $titleOptions),
            '{summary}' => Html::tag('div', '{summary}', $summaryOptions),
        ]), $options);
    }

    /**
     * Generates the toolbar.
     *
     * @return string
     * @throws Exception
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
     * @throws Exception
     */
    protected function renderToolbarContainer()
    {
        $tag = ArrayHelper::remove($this->toolbarContainerOptions, 'tag', 'div');

        /**
         * allow to override the float declaration:
         * forcing float-right only if no float is defined in toolbarContainerOptions
         */
        if (
            !Lib::stripos($this->toolbarContainerOptions['class'], $this->getCssClass(self::BS_PULL_RIGHT))
            && !Lib::stripos($this->toolbarContainerOptions['class'], $this->getCssClass(self::BS_PULL_LEFT))
        ) {
            $this->addCssClass($this->toolbarContainerOptions, self::BS_PULL_RIGHT);
        }

        return Html::tag($tag, $this->renderToolbar(), $this->toolbarContainerOptions);
    }

    /**
     * Generate HTML markup for additional table rows for header and/or footer.
     *
     * @param  array|string  $data  the table rows configuration
     *
     * @return string
     * @throws Exception
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
                    $rows .= "\t".Html::tag($tag, $colContent, $colOptions)."\n";
                }
                $rows .= Html::endTag('tr')."\n";
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
        $this->_toggleOptionsVar = 'kvTogOpts_'.hash('crc32', $opts);
        $view->registerJs("{$this->_toggleOptionsVar}={$opts};");
        GridToggleDataAsset::register($view);
        $this->_toggleScript = "kvToggleData({$this->_toggleOptionsVar});";
    }

    /**
     * Registers client assets for the [[GridView]] widget.
     * @throws Exception
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
        if ($this->export !== false && is_array($this->export) && !empty($this->export)) {
            GridExportAsset::register($view);
            if (!isset($this->_module->downloadAction)) {
                $action = ["/{$this->moduleId}/export/download"];
            } else {
                $action = (array)$this->_module->downloadAction;
            }
            $gridOpts = Json::encode(
                [
                    'gridId' => $gridId,
                    'action' => Url::to($action),
                    'module' => $this->moduleId,
                    'encoding' => ArrayHelper::getValue($this->export, 'encoding', 'utf-8'),
                    'bom' => (int)ArrayHelper::getValue($this->export, 'bom', 1),
                    'target' => ArrayHelper::getValue($this->export, 'target', self::TARGET_BLANK),
                    'messages' => $this->export['messages'],
                    'exportConversions' => $this->exportConversions,
                    'skipExportElements' => $this->export['skipExportElements'],
                    'showConfirmAlert' => ArrayHelper::getValue($this->export, 'showConfirmAlert', true),
                ]
            );
            $gridOptsVar = 'kvGridExp_'.hash('crc32', $gridOpts);
            $view->registerJs("var {$gridOptsVar}={$gridOpts};");
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
                $genOptsVar = 'kvGridExp_'.hash('crc32', $genOpts);
                $view->registerJs("var {$genOptsVar}={$genOpts};");
                $expOpts = Json::encode(
                    [
                        'dialogLib' => ArrayHelper::getValue($this->krajeeDialogSettings, 'libName', 'krajeeDialog'),
                        'gridOpts' => new JsExpression($gridOptsVar),
                        'genOpts' => new JsExpression($genOptsVar),
                        'alertMsg' => ArrayHelper::getValue($setting, 'alertMsg', false),
                        'config' => ArrayHelper::getValue($setting, 'config', []),
                    ]
                );
                $expOptsVar = 'kvGridExp_'.hash('crc32', $expOpts);
                $view->registerJs("var {$expOptsVar}={$expOpts};");
                $script .= "{$id}.gridexport({$expOptsVar});";
            }
        }
        $contId = '#'.$this->containerOptions['id'];
        $container = "jQuery('{$contId}')";
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
        $edited = $this->initEditedRow();
        if (!empty($edited)) {
            GridEditedRowAsset::register($view);
            $opts = Json::encode($edited);
            $script .= "kvGridEditedRow({$opts});";
        }
        $psVar = 'ps_'.Inflector::slug($this->containerOptions['id'], '_');
        if (!empty($this->perfectScrollbar)) {
            GridPerfectScrollbarAsset::register($view);
            $script .= "var {$psVar} = new PerfectScrollbar('{$contId}', ".
                Json::encode($this->perfectScrollbarOptions).');';
        }
        $this->genToggleDataScript();
        $script .= $this->_toggleScript;
        $this->_gridClientFunc = 'kvGridInit_'.hash('crc32', $script);
        $this->options['data-krajee-grid'] = $this->_gridClientFunc;
        $this->options['data-krajee-ps'] = $psVar;
        $view->registerJs("var {$this->_gridClientFunc}=function(){\n{$script}\n};\n{$this->_gridClientFunc}();");
    }

    /**
     * Renders the table header or footer part
     * @param  string  $part  whether thead or tfoot
     * @param  string  $content
     * @return string
     * @throws Exception
     */
    protected function renderTablePart($part, $content)
    {
        $content = Lib::strtr($content, ["<{$part}>\n" => '', "\n</{$part}>" => '', "<{$part}>" => '', "</{$part}>" => '']);
        $token = $part === 'thead' ? 'Header' : 'Footer';
        $prop = Lib::strtolower($token).'Container';
        $options = $this->$prop;
        $before = "before{$token}";
        $after = "after{$token}";
        $out = [];
        if (isset($this->$before)) {
            $out[] = $this->generateRows($this->$before);
        }
        $out[] = $content;
        if (isset($this->$after)) {
            $out[] = $this->generateRows($this->$after);
        }
        $content = implode("\n", $out);

        return Html::tag($part, $content, $options);
    }
}
