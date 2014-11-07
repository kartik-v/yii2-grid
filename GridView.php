<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @package yii2-grid
 * @version 2.3.0
 */

namespace kartik\grid;

use Closure;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\base\InvalidConfigException;
use yii\bootstrap\ButtonDropdown;
use yii\widgets\Pjax;
use yii\web\View;
use kartik\base\Config;

/**
 * Enhances the Yii GridView widget with various options to include Bootstrap
 * specific styling enhancements. Also allows to simply disable Bootstrap styling
 * by setting `bootstrap` to false. Includes an extended data column for column
 * specific enhancements.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class GridView extends \yii\grid\GridView
{

    /**
     * Bootstrap Contextual Color Types
     */
    const TYPE_DEFAULT = 'default'; // only applicable for panel contextual style
    const TYPE_PRIMARY = 'primary';
    const TYPE_INFO = 'info';
    const TYPE_DANGER = 'danger';
    const TYPE_WARNING = 'warning';
    const TYPE_SUCCESS = 'success';
    const TYPE_ACTIVE = 'active'; // only applicable for table row contextual style

    /**
     * Boolean Icons
     */
    const ICON_ACTIVE = '<span class="glyphicon glyphicon-ok text-success"></span>';
    const ICON_INACTIVE = '<span class="glyphicon glyphicon-remove text-danger"></span>';

    /**
     * Alignment
     */
    // Horizontal Alignment
    const ALIGN_RIGHT = 'right';
    const ALIGN_CENTER = 'center';
    const ALIGN_LEFT = 'left';
    // Vertical Alignment
    const ALIGN_TOP = 'top';
    const ALIGN_MIDDLE = 'middle';
    const ALIGN_BOTTOM = 'bottom';
    // CSS for preventing cell wrapping
    const NOWRAP = 'kv-nowrap';

    /**
     * Filter input types
     */
    // input types
    const FILTER_CHECKBOX = 'checkbox';
    const FILTER_RADIO = 'radio';
    // input widget classes
    const FILTER_SELECT2 = '\kartik\widgets\Select2';
    const FILTER_TYPEAHEAD = '\kartik\widgets\Typeahead';
    const FILTER_SWITCH = '\kartik\widgets\SwitchInput';
    const FILTER_SPIN = '\kartik\widgets\TouchSpin';
    const FILTER_STAR = '\kartik\widgets\StarRating';
    const FILTER_DATE = '\kartik\widgets\DatePicker';
    const FILTER_TIME = '\kartik\widgets\TimePicker';
    const FILTER_DATETIME = '\kartik\widgets\DateTimePicker';
    const FILTER_DATE_RANGE = '\kartik\daterange\DateRangePicker';
    const FILTER_SORTABLE = '\kartik\sortinput\SortableInput';
    const FILTER_RANGE = '\kartik\widgets\RangeInput';
    const FILTER_COLOR = '\kartik\widgets\ColorInput';
    const FILTER_SLIDER = '\kartik\slider\Slider';
    const FILTER_MONEY = '\kartik\money\MaskMoney';
    const FILTER_CHECKBOX_X = '\kartik\checkbox\CheckboxX';

    /**
     * Summary Functions
     */
    const F_COUNT = 'count';
    const F_SUM = 'sum';
    const F_MAX = 'max';
    const F_MIN = 'min';
    const F_AVG = 'avg';

    /**
     * Grid Export Formats
     */
    const HTML = 'html';
    const CSV = 'csv';
    const TEXT = 'txt';
    const EXCEL = 'xls';
    const PDF = 'pdf';
    const JSON = 'json';

    /**
     * Grid Layout Templates
     */
    // panel grid template with `footer`, pager in the `footer`, and `summary` in the `heading`.
    const TEMPLATE_1 = <<< HTML
    <div class="panel {type}">
        <div class="panel-heading">
             <div class="pull-right">{summary}</div>
             {heading}
        </div>
         {before}
        {items}
        {after}
        <div class="panel-footer">
            <div class="pull-right">{footer}</div>
            <div class="kv-panel-pager">{pager}</div>
            <div class="clearfix"></div>
        </div>
    </div>
HTML;
    // panel grid template with hidden `footer`, pager in the `after`, and `summary` in the `heading`.
    const TEMPLATE_2 = <<< HTML
    <div class="panel {type}">
        <div class="panel-heading">
             <div class="pull-right">{summary}</div>
             {heading}
        </div>
        {before}
        {items}
        <div class="kv-panel-after">
            <div class="pull-right">{after}</div>
            <div class="kv-panel-pager">{pager}</div>
            <div class="clearfix"></div>
        </div>
    </div>
HTML;

    /**
     * @var string the template for rendering the {before} part in the layout templates.
     * The following special variables are recognized and will be replaced:
     * - {toolbar}, string which will render the [[$toolbar]] property passed
     * - {export}, string which will render the [[$export]] menu button content
     * - {beforeContent}, string which will render the [[$before]] text passed in the panel settings
     */
    public $beforeTemplate = <<< HTML
<div class="pull-right">
    <div class="btn-toolbar kv-grid-toolbar" role="toolbar">
        {toolbar}
    </div>    
</div>
{beforeContent}
<div class="clearfix"></div>
HTML;

    /**
     * @var string the template for rendering the {after} part in the layout templates.
     * The following special variables are recognized and will be replaced:
     * - {toolbar}, string which will render the [[$toolbar]] property passed
     * - {export}, string which will render the [[$export]] menu button content
     * - {afterContent}, string which will render the [[$after]] text passed in the panel settings
     */
    public $afterTemplate = '{afterContent}';

    /**
     * @var array|string, configuration of additional header table rows that will be rendered before the default grid
     * header row. If set as a string, it will be displayed as is, without any HTML encoding. If set as an array, each
     * row in this array corresponds to a HTML table row, where you can configure the columns with these properties:
     * - columns: array, the header row columns configuration where you can set the following properties:
     *      - content: string, the table cell content for the column
     *      - tag: string, the tag for rendering the table cell. If not set, defaults to 'th'.
     *      - options: array, the HTML attributes for the table cell
     * - options: array, the HTML attributes for the table row
     */
    public $beforeHeader = [];

    /**
     * @var array|string, configuration of additional header table rows that will be rendered after default grid
     * header row. If set as a string, it will be displayed as is, without any HTML encoding. If set as an array, each
     * row in this array corresponds to a HTML table row, where you can configure the columns with these properties:
     * - columns: array, the header row columns configuration where you can set the following properties:
     *      - content: string, the table cell content for the column
     *      - tag: string, the tag for rendering the table cell. If not set, defaults to 'th'.
     *      - options: array, the HTML attributes for the table cell
     * - options: array, the HTML attributes for the table row
     */
    public $afterHeader = [];

    /**
     * @var array|string, configuration of additional footer table rows that will be rendered before the default grid
     * footer row. If set as a string, it will be displayed as is, without any HTML encoding. If set as an array, each
     * row in this array corresponds to a HTML table row, where you can configure the columns with these properties:
     * - columns: array, the footer row columns configuration where you can set the following properties:
     *      - content: string, the table cell content for the column
     *      - tag: string, the tag for rendering the table cell. If not set, defaults to 'th'.
     *      - options: array, the HTML attributes for the table cell
     * - options: array, the HTML attributes for the table row
     */
    public $beforeFooter = [];

    /**
     * @var array|string, configuration of additional footer table rows that will be rendered after the default grid
     * footer row. If set as a string, it will be displayed as is, without any HTML encoding. If set as an array, each
     * row in this array corresponds to a HTML table row, where you can configure the columns with these properties:
     * - columns: array, the footer row columns configuration where you can set the following properties:
     *      - content: string, the table cell content for the column
     *      - tag: string, the tag for rendering the table cell. If not set, defaults to 'th'.
     *      - options: array, the HTML attributes for the table cell
     * - options: array, the HTML attributes for the table row
     */
    public $afterFooter = [];

    /**
     * @var array|string the toolbar content configuration. Can be setup as a string or an array.
     * - if set as a string, it will be rendered as is.
     * - if set as an array, each line item will be considered as following
     *   - if the line item is setup as a string, it will be rendered as is
     *   - if the line item is an array it will be parsed for the following keys:
     *      - content: the content to be rendered as a bootstrap button group. The following special
     *        variables are recognized and will be replaced:
     *          - {export}, string which will render the [[$export]] menu button content.
     *          - {toggleData}, string which will render the button to toggle between page data and all data.
     *      - options: the HTML attributes for the button group div container. By default the
     *        CSS class `btn-group` will be attached to this container.
     */
    public $toolbar = [
        '{toggleData}',
        '{export}',
    ];

    /**
     * @var array the settings for the toggle data button for the toggle data type. This will be setup as 
     * an associative array of $type => $options, where $type can be:
     * - 'all': for showing all grid data
     * - 'page': for showing first page data
     * and $options is the HTML attributes for the button. The following special options are recognized:
     * - icon: string the glyphicon suffix name. If not set or empty will not be displayed.
     * - label: string the label for the button.
     *
     * This defaults to the following setting:
     *      [
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
     */
     
    public $toggleDataOptions = [];
    
    /**
     * Tags to replace in the rendered layout. Enter this as `$key => $value` pairs, where:
     * - $key: string, defines the flag.
     * - $value: string|Closure, the value that will be replaced. You can set it as a callback
     *   function to return a string of the signature:
     *      function ($widget) { return 'custom'; }
     * 
     * For example:
     * ['{flag}' => '<span class="glyphicon glyphicon-asterisk"></span']
     * @var array
     */
    public $replaceTags = [];

    /**
     * @var string the default data column class if the class name is not
     * explicitly specified when configuring a data column.
     * Defaults to 'kartik\grid\DataColumn'.
     */
    public $dataColumnClass = 'kartik\grid\DataColumn';

    /**
     * @var array the HTML attributes for the grid footer row
     */
    public $footerRowOptions = ['class' => 'kv-table-footer'];

    /**
     * @var array the HTML attributes for the grid table caption
     */
    public $captionOptions = ['class' => 'kv-table-caption'];

    /**
     * @var array the HTML attributes for the grid table element
     */
    public $tableOptions = [];

    /**
     * @var boolean whether the grid view will be rendered within a pjax container.
     * Defaults to `false`. If set to `true`, the entire GridView widget will be parsed
     * via Pjax and auto-rendered inside a yii\widgets\Pjax widget container. If set to
     * `false` pjax will be disabled and none of the pjax settings will be applied.
     */
    public $pjax = false;

    /**
     * @var array the pjax settings for the widget. This will be considered only when
     * [[pjax]] is set to true. The following settings are recognized:
     * - `neverTimeout`: boolean, whether the pjax request should never timeout. Defaults to `true`.
     *    The pjax:timeout event will be configured to disable timing out of pjax requests for the pjax
     *    container.
     * - `options`: array, the options for the [[yii\widgets\Pjax]] widget.
     * - `loadingCssClass`: boolean/string, the CSS class to be applied to the grid when loading via pjax.
     *    If set to `false` - no css class will be applied. If it is empty, null, or set to `true`, will
     *    default to `kv-grid-loading`.
     * - `beforeGrid`: string, any content to be embedded within pjax container before the Grid widget.
     * - `afterGrid`: string, any content to be embedded within pjax container after the Grid widget.
     */
    public $pjaxSettings = [];

    /**
     * @var boolean whether the grid view will have Bootstrap table styling.
     */
    public $bootstrap = true;

    /**
     * @var boolean whether the grid table will have a `bordered` style.
     * Applicable only if `bootstrap` is `true`. Defaults to `true`.
     */
    public $bordered = true;

    /**
     * @var boolean whether the grid table will have a `striped` style.
     * Applicable only if `bootstrap` is `true`. Defaults to `true`.
     */
    public $striped = true;

    /**
     * @var boolean whether the grid table will have a `condensed` style.
     * Applicable only if `bootstrap` is `true`. Defaults to `false`.
     */
    public $condensed = false;

    /**
     * @var boolean whether the grid table will have a `responsive` style.
     * Applicable only if `bootstrap` is `true`. Defaults to `true`.
     */
    public $responsive = true;

    /**
     * @var boolean whether the grid table will highlight row on `hover`.
     * Applicable only if `bootstrap` is `true`. Defaults to `false`.
     */
    public $hover = false;

    /**
     * @var boolean whether the grid table will have a floating table header.
     * Defaults to `false`.
     */
    public $floatHeader = false;

    /**
     * @var array the plugin options for the floatThead plugin that would render
     * the floating/sticky table header behavior. The default offset from the
     * top of the window where the floating header will 'stick' when scrolling down
     * is set to `50` assuming a fixed bootstrap navbar on top. You can set this to 0
     * or any javascript function/expression.
     * @see http://mkoryak.github.io/floatThead#options
     */
    public $floatHeaderOptions = ['scrollingTop' => 50];

    /**
     * @var array the panel settings. If this is set, the grid widget
     * will be embedded in a bootstrap panel. Applicable only if `bootstrap`
     * is `true`. The following array keys are supported:
     * - `heading`: string, the panel heading. If not set, will not be displayed.
     * - `type`: string, the panel contextual type (one of the TYPE constants,
     *    if not set will default to `default` or `self::TYPE_DEFAULT`),
     * - `footer`: string, the panel footer. If not set, will not be displayed.
     * - 'before': string, content to be placed before/above the grid table (after the header).
     * - `beforeOptions`: array, HTML attributes for the `before` text. If the
     *   `class` is not set, it will default to `kv-panel-before`.
     * - 'after': string, any content to be placed after/below the grid table (before the footer).
     * - `afterOptions`: array, HTML attributes for the `after` text. If the
     *   `class` is not set, it will default to `kv-panel-after`.
     * - `showFooter`: boolean, whether to always show the footer. If so the,
     *    layout will default to the constant `self::TEMPLATE_1`. If this is
     *    set to false, the `pager` will be enclosed within the `kv-panel-after`
     *    container. Defaults to `false`.
     * - `layout`: string, the grid layout to be used if you are using a panel,
     *    If not set, defaults to the constant `self::TEMPLATE_1` if
     *    `showFooter` is `true. If `showFooter` is set to `false`, this will
     *    default to the constant `self::TEMPLATE_2`.
     */
    public $panel = [];

    /**
     * @var boolean whether to show the page summary row for the table. This will
     * be displayed above the footer.
     */
    public $showPageSummary = false;

    /**
     * @array the HTML attributes for the summary row
     */
    public $pageSummaryRowOptions = ['class' => 'kv-page-summary warning'];

    /**
     * @array|boolean the grid export menu settings. Displays a Bootstrap dropdown menu that allows you to export the grid as
     * either html, csv, or excel. If set to false, will not be displayed. The following options can be set:
     * - label: string,the export menu label (this is not HTML encoded). Defaults to ''.
     * - icon: string,the glyphicon suffix to be displayed before the export menu label. If not set or is an empty string, this
     *   will not be displayed. Defaults to 'export'.
     * - iconOptions: array, the HTML options for the icon.
     * - messages: array, the configuration of various messages that will be displayed at runtime:
     *     - allowPopups: string, the message to be shown to disable browser popups for download. Defaults to `Disable any popup blockers in your browser to ensure proper download.`.
     *     - confirmDownload: string, the message to be shown for confirming to proceed with the download. Defaults to `Ok to proceed?`.
     *     - downloadProgress: string, the message to be shown in a popup dialog when download request is triggered. Defaults to `Generating file. Please wait...`.
     *     - downloadComplete: string, the message to be shown in a popup dialog when download request is completed. Defaults to 
     *       `All done! Click anywhere here to close this window, once you have downloaded the file.`.
     * - header: string, the header for the page data export dropdown. If set to empty string will not be displayed. Defaults to:
     *   `<li role="presentation" class="dropdown-header">Export Page Data</li>`.
     * - fontAwesome: boolean, whether to use font awesome file type icons. Defaults to `false`. If you set it to `true`, then font awesome
     *   icons css class will be applied instead of glyphicons.
     * - items: array, any additional items that will be merged with the export dropdown list. This should be similar to the `items`
     *   property as supported by `\yii\bootstrap\ButtonDropdown` widget. Note the page export items will be automatically 
     *   generated based on settings in the `exportConfig` property.
     * - options: array, HTML attributes for the export menu button. Defaults to `['class' => 'btn btn-default', 'title'=>'Export']`.
     * - encoding: string, the export output file encoding. If not set, defaults to `utf-8`.
     * - menuOptions: array, HTML attributes for the export dropdown menu. Defaults to `['class' => 'dropdown-menu dropdown-menu-right']`. 
     *   This is to be set exactly as the options property for `\yii\bootstrap\Dropdown` widget.
     */
    public $export = [];
    
    /**
     * @var array the configuration for each export format. The array keys must be the one of the `format` constants
     * (CSV, HTML, TEXT, EXCEL, PDF, JSON) and the array value is a configuration array consisiting of these settings:
     * - label: string,the label for the export format menu item displayed
     * - icon: string,the glyphicon or font-awesome name suffix to be displayed before the export menu item label. 
     *   If set to an empty string, this will not be displayed. Refer `defaultConfig` in `initExport` method for default settings.
     * - showHeader: boolean, whether to show table header row in the output. Defaults to `true`.
     * - showPageSummary: boolean, whether to show table page summary row in the output. Defaults to `true`.
     * - showFooter: boolean, whether to show table footer row in the output. Defaults to `true`.
     * - showCaption: boolean, whether to show table caption in the output (only for HTML). Defaults to `true`.
     * - filename: the base file name for the generated file. Defaults to 'grid-export'. This will be used to generate a default
     *   file name for downloading (extension will be one of csv, html, or xls - based on the format setting).
     * - alertMsg: string, the message prompt to show before saving. If this is empty or not set it will not be displayed.
     * - options: array, HTML attributes for the export format menu item.
     * - mime: string, the mime type (for the file format) to be set before downloading.
     * - config: array, the special configuration settings specific to each file format/type. The following configuration options are read specific to each file type:
     *     - HTML:
     *          - cssFile: string, the css file that will be used in the exported HTML file. Defaults to:
     *            `http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css`.
     *     - CSV and TEXT:
     *          - colDelimiter: string, the column delimiter string for TEXT and CSV downloads.
     *          - rowDelimiter: string, the row delimiter string for TEXT and CSV downloads.
     *     - EXCEL:
     *          - worksheet: string, the name of the worksheet, when saved as EXCEL file.
     *     - PDF:
     *          Supports all configuration properties as required in \kartik\mpdf\Pdf extension. In addition, the following
     *          additional special options are recognized:
     *          - contentBefore: string, any HTML formatted content that will be embedded in the PDF output before the grid.
     *          - contentAfter: string, any HTML formatted content that will be embedded in the PDF output after the grid.
     *     - JSON:
     *          - colHeads: array, the column heading names to be output in the json file. If not set, it will be autogenerated as 
     *             "col-{i}", where {i} is the column index. If `slugColHeads` is set to `true`, the extension will attempt to autogenerate 
     *             column heads based on table column heading, whereever possible.
     *          - slugColHeads: boolean, whether to auto-generate column identifiers as slugs based on the table column heading name. 
     *             If the table column heading contains characters which cannot be slugified, then the extension will autogenerate the column 
     *             name as "col-{i}".
     *          - jsonReplacer`: array|JsExpression, the JSON replacer property - can be an array or a JS function created using JsExpression. 
     *             Refer the [JSON documentation](https://developer.mozilla.org/en-US/docs/Web/JavaScript/Guide/Using_native_JSON#The_replacer_parameter for details on setting this property.
     *          - indentSpace: int, pretty print json output and indent by number of spaces specified. Defaults to `4`.
     */
    public $exportConfig = [];
    
    /**
     * @var array, conversion of defined patterns in the grid cells as a preprocessing before
     * the gridview is formatted for export. Each array row must consist of the following
     * two keys:
     * - `from`: string, is the pattern to search for in each grid column's cells
     * - `to`: string, is the string to replace the pattern in the grid column cells
     * This defaults to
     * ```
     * [
     *      ['from'=>GridView::ICON_ACTIVE, 'to'=>Yii::t('kvgrid', 'Active')],
     *      ['from'=>GridView::ICON_INACTIVE, 'to'=>Yii::t('kvgrid', 'Inactive')]
     * ]
     * ```
     */
    public $exportConversions = [];

    /**
     * @var array|boolean the HTML attributes for the grid container. The grid table items
     * will be wrapped in a `div` container with the configured HTML attributes. The ID for
     * the container will be auto generated.
     */
    public $containerOptions = [];

    /**
     * @var boolean whether to export the full grid data using yii2-export extension. 
     * This property is only internally used for exporting the complete data by 
     * the export action.
     */
    public $isFullExport = false;
    
    /**
     * @var string the generated javascript for grid export initialization
     */
    protected $_jsExportScript = '';

    /**
     * @var string the generated javascript for toggling grid data
     */
    protected $_jsToggleScript = '';

    /**
     * @var string the generated javascript for grid float table header initialization
     */
    protected $_jsFloatTheadScript = '';

    /**
     * @var Module the grid module.
     */
    protected $_module;

    /**
     * @var string key to identify showing all data 
     */
    protected $_toggleDataKey; 
    
    /**
     * @var bool whether the current mode is showing all data
     */
    protected $_isShowAll = false; 
    
    /**
     * @inherit doc
     */
    public function init()
    {
        $this->_module = Yii::$app->getModule('gridview');
        if ($this->_module == null || !$this->_module instanceof \kartik\grid\Module) {
            throw new InvalidConfigException('The "gridview" module MUST be setup in your Yii configuration file and assigned to "\kartik\grid\Module" class.');
        }
        if ($this->isFullExport) {
            $this->dataProvider->pagination = false;
            $this->filterModel = null;
            $this->floatHeader = false;
            $this->export = false;
            $this->pjax = false;
        }
        if (empty($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }
        $this->_toggleDataKey = $this->options['id'] . '-toggle-data';
        if (isset($_POST[$this->_toggleDataKey])) {
            $this->_isShowAll = $_POST[$this->_toggleDataKey];
        } else {
            $this->_isShowAll = false;
        }
        if ($this->_isShowAll == true) {
            $this->dataProvider->pagination = false;
        }
        $this->_jsToggleScript = "toggleGridData('{$this->_toggleDataKey}');";
        parent::init();
    }
    
    /**
     * @inherit doc
     * @throws InvalidConfigException
     */
    public function run()
    {
        $this->initToggleData();
        $this->initExport();
        if (isset($this->exportConfig[self::PDF])) {
            Config::checkDependency('mpdf\Pdf', 'yii2-mpdf', 'for PDF export functionality');
        }
        $this->initHeader();
        $this->initBootstrapStyle();
        $this->containerOptions['id'] = $this->options['id'] . '-container';
        if (!$this->isFullExport) {
            $this->registerAssets();
        }
        $this->renderPanel();
        $this->initLayout();
        if ($this->isFullExport) {
            parent::run();
            return;
        }
        $this->beginPjax();
        parent::run();
        $this->endPjax();
    }
    
    /**
     * Initialize grid export
     */
    protected function initExport()
    {
        if ($this->export === false) {
            return;
        }
        $this->exportConversions = ArrayHelper::merge([
            ['from' => self::ICON_ACTIVE, 'to' => Yii::t('kvgrid', 'Active')],
            ['from' => self::ICON_INACTIVE, 'to' => Yii::t('kvgrid', 'Inactive')]
        ], $this->exportConversions);

        $this->export = ArrayHelper::merge([
            'label' => '',
            'icon' => 'export',
            'messages' => [
                'allowPopups' => Yii::t('kvgrid', 'Disable any popup blockers in your browser to ensure proper download.'),
                'confirmDownload' => Yii::t('kvgrid', 'Ok to proceed?'),
                'downloadProgress' => Yii::t('kvgrid', 'Generating the export file. Please wait...'),
                'downloadComplete' => Yii::t('kvgrid', 'Request submitted! You may safely close this dialog after saving your downloaded file.'),
            ],
            'options' => ['class' => 'btn btn-default', 'title' => Yii::t('kvgrid', 'Export')],
            'menuOptions' => ['class' => 'dropdown-menu dropdown-menu-right '],
        ], $this->export);
        if (!isset($this->export['header'])) {
            $this->export['header'] = '<li role="presentation" class="dropdown-header">' . Yii::t('kvgrid', 'Export Page Data'). '</li>';
        }
        if (!isset($this->export['headerAll'])) {
            $this->export['headerAll'] = '<li role="presentation" class="dropdown-header">' . Yii::t('kvgrid', 'Export All Data'). '</li>';
        }
        if (!isset($this->export['fontAwesome'])) {
            $this->export['fontAwesome'] = false;
        }
        $title = empty($this->caption) ? Yii::t('kvgrid', 'Grid Export') : $this->caption;
        $pdfHeader = [
            'L' => [
              'content' => Yii::t('kvgrid', 'Yii2 Grid Export (PDF)'),
              'font-size' => 8,
              'color'=>'#333333'
            ],
            'C' => [
              'content' => $title,
              'font-size' => 16,
              'color'=>'#333333'
            ],
            'R' => [
              'content' => Yii::t('kvgrid', 'Generated') . ': ' . date("D, d-M-Y g:i a T"),
              'font-size' => 8,
              'color'=>'#333333'
            ]
        ];
        $pdfFooter = [
            'L' => [
              'content' => Yii::t('kvgrid', "© Krajee Yii2 Extensions"),
              'font-size' => 8,
              'font-style' => 'B',
              'color'=>'#999999'
            ],
            'R' => [
              'content' => '[ {PAGENO} ]',
              'font-size' => 10,
              'font-style' => 'B',
              'font-family' => 'serif',
              'color'=>'#333333'
            ],
            'line' => true,
        ];
        $isFa = $this->export['fontAwesome'];
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
                    'cssFile' => 'http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css'
                ]
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
                    'colDelimiter' => ",",
                    'rowDelimiter' => "\r\n",
                ]
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
                ]
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
                    'cssFile' => ''
                ]
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
                    'mode' => 'c',
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
                            ['odd' => $pdfHeader, 'even' => $pdfHeader]
                        ],
                        'SetFooter' => [
                            ['odd' => $pdfFooter, 'even' => $pdfFooter]
                        ],
                    ],
                    'options' => [
                        'title' => $title,
                        'subject' => Yii::t('kvgrid', 'PDF export generated by kartik-v/yii2-grid extension'),
                        'keywords' => Yii::t('kvgrid', 'krajee, grid, export, yii2-grid, pdf')
                    ],
                    'contentBefore'=>'',
                    'contentAfter'=>''
                ]
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
                    'jsonReplacer' => null,
                    'indentSpace' => 4
                ]
            ],
        ];
        $this->exportConfig = self::parseExportConfig($this->exportConfig, $defaultExportConfig);
    }
    
    
    /**
     * Initialize toggle data button options
     */
    protected function initToggleData()
    {
        $defaultOptions = [
            'all' => [
                'icon' => 'resize-full',
                'label' => Yii::t('kvgrid', 'All'),
                'class' => 'btn btn-default',
                'title' => Yii::t('kvgrid', 'Show all data')
            ],
            'page' => [
                'icon' => 'resize-small',
                'label' => Yii::t('kvgrid', 'Page'),
                'class' => 'btn btn-default',
                'title' => Yii::t('kvgrid', 'Show first page data')
            ],      
        ];
        if (empty($this->toggleDataOptions['page'])) {
            $this->toggleDataOptions['page'] = $defaultOptions['page'];
        } 
        if (empty($this->toggleDataOptions['all'])) {
            $this->toggleDataOptions['all'] = $defaultOptions['all'];
        }
        $tag = $this->_isShowAll ? 'page' : 'all';
        $icon = ArrayHelper::remove($this->toggleDataOptions[$tag], 'icon' , '');
        $options = $this->toggleDataOptions[$tag];
        if (!isset($options['label'])) {
            $label = $defaultOptions[$tag]['label'];
        } else {
            $label = ArrayHelper::remove($options, 'label' , '');
        }
        if (!empty($icon)) {
            $label = "<i class='glyphicon glyphicon-{$icon}'></i> " . $label;
        }
        $this->toggleDataOptions[$tag]['label'] = $label;
        if (!isset($this->toggleDataOptions['title'])) {
            $this->toggleDataOptions['title'] = $defaultOptions[$tag]['title'];
        }
    }
    
    /**
     * Parses export configuration and returns the merged defaults
     * @return array
     */
    protected static function parseExportConfig($exportConfig, $defaultExportConfig) {
        $config = $exportConfig;
        if (is_array($exportConfig) && !empty($exportConfig)) {
            foreach ($exportConfig as $format => $setting) {
                $setup = is_array($exportConfig[$format]) ? $exportConfig[$format] : [];
                $exportConfig[$format] = empty($setup) ? $defaultExportConfig[$format] :
                    ArrayHelper::merge($defaultExportConfig[$format], $setup);
            }
            $config = $exportConfig;
        } else {
            $config = $defaultExportConfig;
        }
        foreach ($config as $format => $setting) {
            $config[$format]['options']['data-pjax'] = false;
        }
        return $config;
    }
    
    /**
     * Initialize bootstrap styling
     */
    protected function initBootstrapStyle()
    {
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
        if ($this->responsive) {
            Html::addCssClass($this->containerOptions, 'table-responsive');
        }    
    }
    
    /**
     * Initialize table header
     */
    protected function initHeader()
    {
        if ($this->filterPosition === self::FILTER_POS_HEADER) {
            // Float header plugin misbehaves when Filter is placed on the first row
            // So disable it when `filterPosition` is `header`.
            $this->floatHeader = false;
        }
    }
    
    /**
     * Initalize grid layout
     */
    protected function initLayout()
    {
        $export = $this->renderExport();
        $toggleData = $this->renderToggleData();
        $toolbar = strtr($this->renderToolbar(), [
            '{export}' => $export,
            '{toggleData}' => $toggleData
        ]);
        $replace = ['{toolbar}' => $toolbar];
        if (strpos($this->layout, '{export}') > 0) {
            $replace['{export}'] = $export;
        } 
        if (strpos($this->layout, '{toggleData}') > 0) {
            $replace['{toggleData}'] = $toggleData;
        } 
        $this->layout = strtr($this->layout, $replace);
        $this->layout = str_replace('{items}', Html::tag('div', '{items}', $this->containerOptions), $this->layout);
        if (is_array($this->replaceTags) && !empty($this->replaceTags)) {
            foreach ($this->replaceTags as $key => $value) {
                if ($value instanceof Closure) {
                    $value = call_user_func($value, $this);
                }
                $this->layout = str_replace($key, $value, $this->layout);
            }
        }
    }
    
    /**
     * Begins the PJAX container
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
        if (ArrayHelper::getvalue($this->pjaxSettings, 'neverTimeout', true)) {
            $view->registerJs("{$container}.on('pjax:timeout', function(e){e.preventDefault()});");
        }
        $loadingCss = ArrayHelper::getvalue($this->pjaxSettings, 'loadingCssClass', 'kv-grid-loading');
        $postPjaxJs = $this->_jsToggleScript;
        if ($loadingCss !== false) {
            $grid = 'jQuery("#' . $this->containerOptions['id'] . '")';
            if ($loadingCss === true) {
                $loadingCss = 'kv-grid-loading';
            }
            $view->registerJs("{$container}.on('pjax:send', function(){{$grid}.addClass('{$loadingCss}')});");
            $postPjaxJs .= "\n{$grid}.removeClass('{$loadingCss}');";
        }
        if (!empty($this->_jsExportScript)) {
            $id = 'jQuery("#' . $this->id . ' .export-csv")';
            $postPjaxJs .= "\n{$this->_jsExportScript}";
        }
        if (!empty($this->_jsFloatTheadScript)) {
            $postPjaxJs .= "\n{$this->_jsFloatTheadScript}";
        }
        if (!empty($postPjaxJs)) {
            $view->registerJs("{$container}.on('pjax:complete', function(){{$postPjaxJs}});");
        }
        Pjax::begin($this->pjaxSettings['options']);
        echo ArrayHelper::getValue($this->pjaxSettings, 'beforeGrid', '');
    }

     
    /**
     * Ends the PJAX container
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
     * Sets the grid layout based on the template and panel settings
     */
    protected function renderPanel()
    {
        if (!$this->bootstrap || !is_array($this->panel) || empty($this->panel)) {
            return;
        }
        $heading = ArrayHelper::getValue($this->panel, 'heading', '');
        $type = 'panel-' . ArrayHelper::getValue($this->panel, 'type', 'default');
        $footer = ArrayHelper::getValue($this->panel, 'footer', '');
        $showFooter = ArrayHelper::getValue($this->panel, 'showFooter', false);
        $template = ($showFooter) ? self::TEMPLATE_1 : self::TEMPLATE_2;
        $layout = ArrayHelper::getValue($this->panel, 'layout', $template);
        $before = ArrayHelper::getValue($this->panel, 'before', '');
        $after = ArrayHelper::getValue($this->panel, 'after', '');
        $beforeOptions = ArrayHelper::getValue($this->panel, 'beforeOptions', []);
        $afterOptions = ArrayHelper::getValue($this->panel, 'afterOptions', []);

        if ($before != '') {
            if (empty($beforeOptions['class'])) {
                $beforeOptions['class'] = 'kv-panel-before';
            }
            $content = strtr($this->beforeTemplate, ['{beforeContent}' => $before]);
            $before = Html::tag('div', $content, $beforeOptions);
        }
        if ($after != '' && $layout != self::TEMPLATE_2) {
            if (empty($afterOptions['class'])) {
                $afterOptions['class'] = 'kv-panel-after';
            }
            $content = strtr($this->afterTemplate, ['{afterContent}' => $after]);
            $after = Html::tag('div', $content, $afterOptions);
        }
        $this->layout = strtr($layout, [
            '{heading}' => $heading,
            '{type}' => $type,
            '{footer}' => $footer,
            '{before}' => $before,
            '{after}' => $after
        ]);
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
        foreach ($this->columns as $column) {
            $cells[] = $column->renderPageSummaryCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->pageSummaryRowOptions);
        return "<tfoot>\n" . $content . "\n</tfoot>";
    }

    /**
     * Renders the table body.
     *
     * @return string the rendering result.
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
     * Generates the toolbar
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
                Html::addCssClass($options, 'btn-group');
                $toolbar .= Html::tag('div', $content, $options);
            } else {
                $toolbar .= "\n{$item}";
            }
        }
        return $toolbar;
    }

    /**
     * Renders the toggle data button
     *
     * @return string
     */
    public function renderToggleData() {
        $tag = $this->_isShowAll ? 'page' : 'all';
        $id = $this->_toggleDataKey;
        $label = ArrayHelper::remove($this->toggleDataOptions[$tag], 'label', '');
        $input = Html::checkbox($id, $this->_isShowAll, ['id'=>$id, 'style'=>'display:none']);
        return '<div class="btn-group">' . Html::beginForm('', 'post', []) . Html::label($label, $id, $this->toggleDataOptions[$tag]) . $input . '</form></div>';
    }
    
    /**
     * Renders the export menu
     *
     * @return string
     */
    public function renderExport()
    {
        if ($this->export === false || !is_array($this->export) || 
            empty($this->exportConfig) || !is_array($this->exportConfig)) {
            return '';
        }
        $title = $this->export['label'];
        $icon = $this->export['icon'];
        $options = $this->export['options'];
        $menuOptions = $this->export['menuOptions'];
        $title = ($icon == '') ? $title : "<i class='glyphicon glyphicon-{$icon}'></i> {$title}";
        $action = Yii::$app->getModule('gridview')->downloadAction;
        if (!is_array($action)) {
            $action = [$action];
        }
        $encoding = ArrayHelper::getValue($this->export, 'encoding', 'utf-8');
        $form = Html::beginForm($action, 'post', [
            'class' => 'kv-export-form',
            'style' => 'display:none',
            'target' => 'kvDownloadDialog',
            'data-pjax' => false
        ]) . "\n" .
        Html::hiddenInput('export_filetype') . "\n" .
        Html::hiddenInput('export_filename') ."\n" .
        Html::hiddenInput('export_mime') . "\n" .
        Html::hiddenInput('export_config') . "\n" .
        Html::hiddenInput('export_encoding', $encoding) . "\n" .
        Html::textArea('export_content') . "\n</form>";
        $items = empty($this->export['header']) ? [] : [$this->export['header']];
        $iconPrefix = $this->export['fontAwesome'] ? 'fa fa-' : 'glyphicon glyphicon-';
        foreach ($this->exportConfig as $format => $setting) {
            $iconOptions = ArrayHelper::getValue($setting, 'iconOptions', []);
            Html::addCssClass($iconOptions, $iconPrefix . $setting['icon']);
            $label = (empty($setting['icon']) || $setting['icon'] == '') ? $setting['label'] : Html::tag('i', '', $iconOptions) . ' ' . $setting['label'];
            $items[] = [
                'label' => $label,
                'url' => '#',
                'linkOptions' => ['class' => 'export-' . $format, 'data-format' => ArrayHelper::getValue($setting, 'mime', 'text/plain')],
                'options' => $setting['options']
            ];
        }
        return ButtonDropdown::widget([
            'label' => $title,
            'dropdown' => ['items' => $items, 'encodeLabels' => false, 'options'=>$menuOptions],
            'options' => $options,
            'encodeLabel' => false
        ]) . $form;
    }
    
    /**
     * Renders the table header.
     *
     * @return string the rendering result.
     */
    public function renderTableHeader()
    {
        $content = parent::renderTableHeader();
        return strtr($content, [
            '<thead>' => "<thead>\n" . $this->generateRows($this->beforeHeader),
            '</thead>' => $this->generateRows($this->afterHeader) . "\n</thead>",
        ]);
    }

    /**
     * Renders the table footer.
     *
     * @return string the rendering result.
     */
    public function renderTableFooter()
    {
        $content = parent::renderTableFooter();
        return strtr($content, [
            '<tfoot>' => "<tfoot>\n" . $this->generateRows($this->beforeFooter),
            '</tfoot>' => $this->generateRows($this->afterFooter) . "\n</tfoot>",
        ]);
    }

    /**
     * Generate HTML markup for additional table rows for header and/or footer
     *
     * @param array|string $data the table rows configuration
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
                    $rows .= "\t" . Html::tag('th', $colContent, $colOptions) . "\n";
                }
                $rows .= Html::endTag('tr') . "\n";
            }
        }
        return $rows;
    }

    /**
     * Registers client assets
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        GridViewAsset::register($view);
        $gridId = $this->options['id'];
        $view->registerJs($this->_jsToggleScript);
        
        if ($this->export !== false && is_array($this->export) && !empty($this->export)) {
            GridExportAsset::register($view);
            foreach ($this->exportConfig as $format => $setting) {
                $id = "jQuery('#{$gridId} .export-{$format}')";
                $grid = new JsExpression("jQuery('#{$gridId}')");
                $config = ArrayHelper::getValue($setting, 'config', []);
                $options = [
                    'grid' => $grid,
                    'filename' => $setting['filename'],
                    'showHeader' => $setting['showHeader'],
                    'showPageSummary' => $setting['showPageSummary'],
                    'showFooter' => $setting['showFooter'],
                    'alertMsg' => ArrayHelper::getValue($setting, 'alertMsg', false),
                    'messages' => $this->export['messages'],
                    'exportConversions' => $this->exportConversions,
                    'config' => $config
                ];
                $opts = Json::encode($options);
                $this->_jsExportScript .= "\n{$id}.gridexport({$opts});";
            }
            if (!empty($this->_jsExportScript)) {
                $view->registerJs($this->_jsExportScript);
            }
        }

        if ($this->floatHeader) {
            GridFloatHeadAsset::register($view);
            $this->floatHeaderOptions = ArrayHelper::merge([
                'floatTableClass' => 'kv-table-float',
                'floatContainerClass' => 'kv-thead-float',
            ], $this->floatHeaderOptions);
            $opts = Json::encode($this->floatHeaderOptions);
            $this->_jsFloatTheadScript = "jQuery('#{$gridId} table').floatThead({$opts});";
            $view->registerJs($this->_jsFloatTheadScript);
        }
    }
}
