<?php

/**
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2013
 * @package yii2-grid
 * @version 1.0.0
 */

namespace kartik\grid;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\helpers\Html;
use yii\base\InvalidConfigException;

/**
 * Enhances the Yii GridView widget with various options to include Bootstrap
 * specific styling enhancements. Also allows to simply disable Bootstrap styling
 * by setting `bootstrap` to false. Includes an extended data column for column 
 * specific enhancements.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class GridView extends \yii\Grid\GridView
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

    /**
     * Filter input types
     */
    // input types
    const FILTER_CHECKBOX = 'checkbox';
    const FILTER_RADIO = 'radio';
    // input widget classes
    const FILTER_SELECT2 = '\kartik\widgets\Select2';
    const FILTER_TYPEAHEAD = '\kartik\widgets\Typeahead';
    const FILTER_SWITCH = '\kartik\widgets\Switch';
    const FILTER_SPIN = '\kartik\widgets\TouchSpin';
    const FILTER_STAR = '\kartik\widgets\StarRating';
    const FILTER_DATE = '\kartik\widgets\DatePicker';
    const FILTER_TIME = '\kartik\widgets\TimePicker';
    const FILTER_RANGE = '\kartik\widgets\RangeInput';
    const FILTER_COLOR = '\kartik\widgets\ColorInput';

    /**
     * Grid Layout Templates
     */
    // panel grid template with `footer`, pager in the `footer`, and `summary` in the `heading`.
    const TEMPLATE_1 = <<< EOT
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
EOT;
    // panel grid template with hidden `footer`, pager in the `after`, and `summary` in the `heading`.
    const TEMPLATE_2 = <<< EOT
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
EOT;

    /**
     * Summary Functions
     */
    const F_COUNT = 'count';
    const F_SUM = 'sum';
    const F_MAX = 'max';
    const F_MIN = 'min';
    const F_AVG = 'avg';

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
     * @var array the HTML attributes for the grid footer row
     */
    public $captionOptions = ['class' => 'kv-table-caption'];

    /**
     * @var array the HTML attributes for the grid table element
     */
    public $tableOptions = [];

    /**
     * @var boolean whether the grid view will have a `bootstrap table` style
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
     * @var boolean whether the grid view will have a `responsive table` style. 
     * Applicable only if `bootstrap` is `true`. Defaults to `true`.
     */
    public $responsive = true;

    /**
     * @var boolean whether the grid table will display selected row on `hover`. 
     * Applicable only if `bootstrap` is `true`. Defaults to `false`.
     */
    public $hover = false;

    /**
     * @var boolean whether the grid table will have a floating table header.
     * Defaults to `false`.
     */
    public $floatHeader = false;

    /**
     * @var integer the plugin options for the floatThead plugin that would render 
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
     * - 'before': string, content to be placed before/above the grid table. 
     * - `beforeOptions`: array, HTML attributes for the `before` text. If the
     *   `class` is not set, it will default to `kv-panel-before`.
     * - 'after': string, any content to be placed after/below the grid table. If the
     *   `class` is not set, it will default to `kv-panel-after`.
     * - `afterOptions`: array, HTML attributes for the `after` text. 
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

    public function init()
    {
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
        }
        $this->registerAssets();
        parent:: init();
    }

    public function run()
    {
        $this->renderPanel();
        if ($this->bootstrap && $this->responsive) {
            $this->layout = str_replace('{items}', '<div class="table-responsive">{items}</div>', $this->layout);
        }
        parent::run();
    }

    /**
     * Sets the grid layout based on the template and panel settings
     */
    protected function renderPanel()
    {
        if ($this->bootstrap && !empty($this->panel)) {
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
                $before = Html::tag('div', $before, $beforeOptions);
            }
            if ($after != '' && $layout != self::TEMPLATE_2) {
                if (empty($afterOptions['class'])) {
                    $afterOptions['class'] = 'kv-panel-after';
                }
                $after = Html::tag('div', $after, $afterOptions);
            }
            $this->layout = strtr($layout, [
                '{heading}' => $heading,
                '{type}' => $type,
                '{footer}' => $footer,
                '{before}' => $before,
                '{after}' => $after,
            ]);
        }
    }

    /**
     * Register assets
     */
    protected function registerAssets()
    {
        $view = $this->getView();
        if ($this->floatHeader) {
            GridViewAsset::register($view)->js[] = YII_DEBUG ? 'js/jquery.floatThead.js' : 'js/jquery.floatThead.min.js';
            $this->floatHeaderOptions += [
                'floatTableClass' => 'kv-table-float',
                'floatContainerClass' => 'kv-thead-float',
            ];
            $js = '$("#' . $this->id . ' table").floatThead(' . Json::encode($this->floatHeaderOptions) . ');';
            $view->registerJs($js);
        }
        else {
            GridViewAsset::register($view);
        }
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
    public function formatColumn($hAlign, $vAlign, $width, &$headerOptions, &$contentOptions, &$pageSummaryOptions, &$footerOptions)
    {
        if ($hAlign === self::ALIGN_LEFT || $hAlign === self::ALIGN_RIGHT || $hAlign === self::ALIGN_CENTER) {
            $class = "kv-align-{$hAlign}";
            Html::addCssClass($headerOptions, $class);
            Html::addCssClass($contentOptions, $class);
            Html::addCssClass($pageSummaryOptions, $class);
            Html::addCssClass($footerOptions, $class);
        }
        if ($vAlign === self::ALIGN_TOP || $vAlign === self::ALIGN_MIDDLE || $vAlign === self::ALIGN_BOTTOM) {
            $class = "kv-align-{$vAlign}";
            Html::addCssClass($headerOptions, $class);
            Html::addCssClass($contentOptions, $class);
            Html::addCssClass($pageSummaryOptions, $class);
            Html::addCssClass($footerOptions, $class);
        }
        if (trim($width) != '') {
            Html::addCssStyle($headerOptions, $width);
            Html::addCssStyle($contentOptions, $width);
            Html::addCssStyle($pageSummaryOptions, $width);
            Html::addCssStyle($footerOptions, $width);
        }
    }

    /**
     * Renders the table page summary.
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

}