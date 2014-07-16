<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014
 * @package yii2-grid
 * @version 1.7.0
 */

namespace kartik\grid;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\base\InvalidConfigException;
use yii\bootstrap\ButtonDropdown;

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
    const FILTER_DATE_RANGE = '\kartik\widgets\DateRangePicker';
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
	{toolbar}\n{export}
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
     * @var string the toolbar content to be rendered.
     */
    public $toolbar = '';

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
     * - label: string,the export menu label (this is not HTML encoded). Defaults to 'Export'.
     * - icon: string,the glyphicon suffix to be displayed before the export menu label. If set to an empty string, this
     *   will not be displayed. Defaults to 'export'.
     * - browserPopupsMsg: string, the message to be shown to disable browser popups for download
     * - options: array, HTML attributes for the export menu. Defaults to ['class' => 'btn btn-danger']
     */
    public $export = [];

    /**
     * @var array the configuration for each export format. The array keys must be the one of the `format` constants
     * (CSV, HTML, TEXT, or EXCEL) and the array value is a configuration array consisiting of these settings:
     * - label: string,the label for the export format menu item displayed
     * - icon: string,the glyphicon suffix to be displayed before the export menu item label. If set to an empty string, this
     *   will not be displayed. Defaults to the 'floppy-' glyphicons present in bootstrap.
     * - showHeader: boolean, whether to show table header row in the output. Defaults to `true`.
     * - showPageSummary: boolean, whether to show table page summary row in the output. Defaults to `true`.
     * - showFooter: boolean, whether to show table footer row in the output. Defaults to `true`.
     * - showCaption: boolean, whether to show table caption in the output (only for HTML). Defaults to `true`.
     * - worksheet: string, the name of the worksheet, when saved as excel file.
     * - colDelimiter: string, the column delimiter string for TEXT and CSV downloads.
     * - rowDelimiter: string, the row delimiter string for TEXT and CSV downloads.
     * - filename: the base file name for the generated file. Defaults to 'grid-export'. This will be used to generate a default
     *   file name for downloading (extension will be one of csv, html, or xls - based on the format setting).
     * - alertMsg: string, the message prompt to show before saving. If this is empty or not set it will not be displayed.
     * - cssFile: string, the css file that will be used in the exported HTML file. Defaults to:
     *   `http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css`.
     * - options: array, HTML attributes for the export format menu item.
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
     * will be wrapped in a `div` container with the configured HTML attributes. If
     * set to `false`, the grid table will not be wrapped in a container.
     */
    public $containerOptions = [];
    
    public function init()
    {
        $module = Yii::$app->getModule('gridview');
        if (!is_array($this->containerOptions) && $this->containerOptions !== false) {
            $this->containerOptions = [];
        }
        if ($module == null || !$module instanceof \kartik\grid\Module) {
            throw new InvalidConfigException('The "gridview" module MUST be setup in your Yii configuration file and assigned to "\kartik\grid\Module" class.');
        }
        $this->exportConversions = ArrayHelper::merge([
            ['from'=>self::ICON_ACTIVE, 'to'=>Yii::t('kvgrid', 'Active')],
            ['from'=>self::ICON_INACTIVE, 'to'=>Yii::t('kvgrid', 'Inactive')]
        ], $this->exportConversions);
        
        if ($this->export !== false) {
            $this->export = ArrayHelper::merge([
                'label' => Yii::t('kvgrid', 'Export'),
                'icon' => 'export',
                'browserPopupsMsg' => Yii::t('kvgrid', 'Disable any popup blockers in your browser to ensure proper download.'),
                'options' => ['class' => 'btn btn-danger']
            ], $this->export);
            $defaultExportConfig = [
                self::HTML => [
                    'label' => Yii::t('kvgrid', 'HTML'),
                    'icon' => 'floppy-saved',
                    'showHeader' => true,
                    'showPageSummary' => true,
                    'showFooter' => true,
                    'showCaption' => true,
                    'filename' => Yii::t('kvgrid', 'grid-export'),
                    'alertMsg' => Yii::t('kvgrid', 'The HTML export file will be generated for download.'),
                    'cssFile' => 'http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css',
                    'options' => ['title' => Yii::t('kvgrid', 'Save as HTML')]
                ],
                self::CSV => [
                    'label' => Yii::t('kvgrid', 'CSV'),
                    'icon' => 'floppy-open',
                    'showHeader' => true,
                    'showPageSummary' => true,
                    'showFooter' => true,
                    'showCaption' => true,
                    'colDelimiter' => ",",
                    'rowDelimiter' => "\r\n",
                    'filename' => Yii::t('kvgrid', 'grid-export'),
                    'alertMsg' => Yii::t('kvgrid', 'The CSV export file will be generated for download.'),
                    'options' => ['title' => Yii::t('kvgrid', 'Save as CSV')]
                ],
                self::TEXT => [
                    'label' => Yii::t('kvgrid', 'Text'),
                    'icon' => 'floppy-save',
                    'showHeader' => true,
                    'showPageSummary' => true,
                    'showFooter' => true,
                    'showCaption' => true,
                    'colDelimiter' => "\t",
                    'rowDelimiter' => "\r\n",
                    'filename' => Yii::t('kvgrid', 'grid-export'),
                    'alertMsg' => Yii::t('kvgrid', 'The TEXT export file will be generated for download.'),
                    'options' => ['title' => Yii::t('kvgrid', 'Save as Text')]
                ],
                self::EXCEL => [
                    'label' => Yii::t('kvgrid', 'Excel'),
                    'icon' => 'floppy-remove',
                    'showHeader' => true,
                    'showPageSummary' => true,
                    'showFooter' => true,
                    'showCaption' => true,
                    'worksheet' => Yii::t('kvgrid', 'ExportWorksheet'),
                    'filename' => Yii::t('kvgrid', 'grid-export'),
                    'alertMsg' => Yii::t('kvgrid', 'The EXCEL export file will be generated for download.'),
                    'cssFile' => '',
                    'options' => ['title' => Yii::t('kvgrid', 'Save as Excel')]
                ],
            ];
            $exportConfig = [];
            if (is_array($this->exportConfig) && !empty($this->exportConfig)) {
                foreach ($this->exportConfig as $format => $setting) {
                    $setup = is_array($this->exportConfig[$format]) ? $this->exportConfig[$format] : [];
                    $exportConfig[$format] = empty($setup) ? $defaultExportConfig[$format] :
                        ArrayHelper::merge($defaultExportConfig[$format], $setup);
                }
                $this->exportConfig = $exportConfig;
            } else {
                $this->exportConfig = $defaultExportConfig;
            }
        }
        if ($this->filterPosition === self::FILTER_POS_HEADER) {
            // Float header plugin misbehaves when Filter is placed on the first row
            // So disable it when `filterPosition` is `header`.
            $this->floatHeader = false;
        }
        if ($this->bootstrap) {
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
            if ($this->responsive && $this->containerOptions !== false) {
                Html::addCssClass($this->containerOptions, 'table-responsive');
            }
        }
        parent:: init();
        $this->registerAssets();
    }

    public function run()
    {
        if ($this->bootstrap && !empty($this->panel)) {
            $this->renderPanel();
        }
        if (strpos($this->layout, '{export}') > 0) {
            $this->layout = strtr($this->layout, [
                '{export}' => $this->renderExport(),
                '{toolbar}' => $this->toolbar
            ]);
        } else {
            $this->layout = strtr($this->layout, ['{toolbar}' => $this->toolbar]);
        }
        if ($this->containerOptions !== false) {
            $this->layout = str_replace('{items}', Html::tag('div', '{items}', $this->containerOptions), $this->layout);
        }
        parent::run();
    }

    /**
     * Sets the grid layout based on the template and panel settings
     */
    protected function renderPanel()
    {
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
     * Format the grid column based on bootstrap, align, and width settings
     *
     * @param string $hAlign the horizontal alignment of the grid column ('left', 'center', or 'right')
     * @param string $vAlign the vertical alignment of the grid column ('top', 'middle', or 'bottom')
     * @param string $width the width of the grid column (in px, em, or %)
     * @param array $headerOptions the HTML attributes for the grid column header
     * @param array $contentOptions the HTML attributes for the grid column content
     * @param array $pageSummaryOptions the HTML attributes for the grid column content
     * @param array $footerOptions the HTML attributes for the grid column footer
     */
    public function formatColumn($hAlign, $vAlign, $noWrap, $width, &$headerOptions, &$contentOptions, &$pageSummaryOptions, &$footerOptions)
    {
        if ($hAlign === self::ALIGN_LEFT || $hAlign === self::ALIGN_RIGHT || $hAlign === self::ALIGN_CENTER) {
            $class = "kv-align-{$hAlign}";
            Html::addCssClass($headerOptions, $class);
            Html::addCssClass($contentOptions, $class);
            Html::addCssClass($pageSummaryOptions, $class);
            Html::addCssClass($footerOptions, $class);
        }
        if ($noWrap) {
            Html::addCssClass($headerOptions, self::NOWRAP);
            Html::addCssClass($contentOptions, self::NOWRAP);
            Html::addCssClass($pageSummaryOptions, self::NOWRAP);
            Html::addCssClass($footerOptions, self::NOWRAP);
        }
        if ($vAlign === self::ALIGN_TOP || $vAlign === self::ALIGN_MIDDLE || $vAlign === self::ALIGN_BOTTOM) {
            $class = "kv-align-{$vAlign}";
            Html::addCssClass($headerOptions, $class);
            Html::addCssClass($contentOptions, $class);
            Html::addCssClass($pageSummaryOptions, $class);
            Html::addCssClass($footerOptions, $class);
        }
        if (trim($width) != '') {
            Html::addCssStyle($headerOptions, "width:{$width};");
            Html::addCssStyle($contentOptions, "width:{$width};");
            Html::addCssStyle($pageSummaryOptions, "width:{$width};");
            Html::addCssStyle($footerOptions, "width:{$width};");
        }
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
     * Renders the export menu
     *
     * @return string
     */
    public function renderExport()
    {
        if ($this->export === false || !is_array($this->export)) {
            return '';
        }
        $formats = $this->exportConfig;
        if (empty($formats) || !is_array($formats)) {
            return '';
        }
        $title = $this->export['label'];
        $icon = $this->export['icon'];
        $options = $this->export['options'];
        $items = [];
        foreach ($formats as $format => $setting) {
            $label = (empty($setting['icon']) || $setting['icon'] == '') ? $setting['label'] : '<i class="glyphicon glyphicon-' . $setting['icon'] . '"></i> ' . $setting['label'];
            $items[] = ['label' => $label, 'url' => '#', 'linkOptions' => ['class' => 'export-' . $format], 'options' => $setting['options']];
        }
        $title = ($icon == '') ? $title : "<i class='glyphicon glyphicon-{$icon}'></i> {$title}";
        $action = Yii::$app->getModule('gridview')->downloadAction;
        if (!is_array($action)) {
            $action = [$action];
        }
        $frameId = $this->options['id'] . '_export';
        $form = Html::beginForm($action, 'post', ['class' => 'kv-export-form', 'style' => 'display:none', 'target' => '_blank']) .
            Html::textInput('export_filetype') . Html::textInput('export_filename') . Html::textArea('export_content') . '</form>';
        return ButtonDropdown::widget([
            'label' => $title,
            'dropdown' => ['items' => $items, 'encodeLabels' => false],
            'options' => $options,
            'encodeLabel' => false
        ]) . $form;
    }

    /**
     * Register assets
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        GridViewAsset::register($view);

        if ($this->export !== false && is_array($this->export) && !empty($this->export)) {
            GridExportAsset::register($view);
            $js = '';
            $popup = ArrayHelper::getValue($this->export, 'browserPopupsMsg', '');
            foreach ($this->exportConfig as $format => $setting) {
                $id = '$("#' . $this->id . ' .export-' . $format . '")';
                $grid = new JsExpression('$("#' . $this->id . '")');
                $options = [
                    'grid' => $grid,
                    'filename' => $setting['filename'],
                    'showHeader' => $setting['showHeader'],
                    'showPageSummary' => $setting['showPageSummary'],
                    'showFooter' => $setting['showFooter'],
                    'worksheet' => ArrayHelper::getValue($setting, 'worksheet', ''),
                    'colDelimiter' => ArrayHelper::getValue($setting, 'colDelimiter', ''),
                    'rowDelimiter' => ArrayHelper::getValue($setting, 'rowDelimiter', ''),
                    'alertMsg' => ArrayHelper::getValue($setting, 'alertMsg', false),
                    'browserPopupsMsg' => $popup,
                    'cssFile' => ArrayHelper::getValue($setting, 'cssFile', ''),
                    'exportConversions' => $this->exportConversions
                ];
                $view->registerJs($id . '.gridexport(' . Json::encode($options) . ');');
            }

        }
        if ($this->floatHeader) {
            GridFloatHeadAsset::register($view);
            $this->floatHeaderOptions = ArrayHelper::merge([
                'floatTableClass' => 'kv-table-float',
                'floatContainerClass' => 'kv-thead-float',
            ], $this->floatHeaderOptions);
            $js = '$("#' . $this->id . ' table").floatThead(' . Json::encode($this->floatHeaderOptions) . ');';
            $view->registerJs($js);
        }
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
                $rows = Html::beginTag('tr', $rowOptions);
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
}