<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2023
 * @version   3.5.3
 */

namespace kartik\grid;

use Exception;
use kartik\base\BootstrapInterface;
use kartik\base\BootstrapTrait;
use Yii;
use yii\grid\Column;
use yii\grid\GridView as YiiGridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

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
class GridView extends YiiGridView implements BootstrapInterface, GridViewInterface
{
    use GridViewTrait;
    use BootstrapTrait;

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
     * @var string the default data column class if the class name is not explicitly specified when configuring a data
     * column. Defaults to 'kartik\grid\DataColumn'.
     */
    public $dataColumnClass = 'kartik\grid\DataColumn';

    /**
     * @var array the HTML attributes for the grid caption
     */
    public $captionOptions = ['class' => 'kv-table-caption'];

    /**
     * @var array the HTML attributes for the grid element
     */
    public $tableOptions = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->initGridView();
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $this->prepareGridView();
        if ($this->pjax) {
            $this->beginPjax();
            parent::run();
            $this->endPjax();
        } else {
            parent::run();
        }
    }

    /**
     * @inheritdoc
     * @throws Exception
     */
    public function renderTableBody()
    {
        $content = parent::renderTableBody();
        if ($this->showPageSummary) {
            $summary = $this->renderPageSummary();

            return $this->pageSummaryPosition === self::POS_TOP ? ($summary.$content) : ($content.$summary);
        }

        return $content;
    }

    /**
     * @inheritdoc
     */
    public function renderTableHeader()
    {
        $cells = [];
        foreach ($this->columns as $index => $column) {
            /* @var DataColumn $column */
            if ($this->resizableColumns && $this->persistResize && !isset($column->headerOptions['data-resizable-column-id'])) {
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

        return $this->renderTablePart('thead', $content);
    }

    /**
     * @inheritdoc
     */
    public function renderTableFooter()
    {
        $cells = [];
        foreach ($this->columns as $column) {
            /* @var $column Column */
            $cells[] = $column->renderFooterCell();
        }
        $content = Html::tag('tr', implode('', $cells), $this->footerRowOptions);
        if ($this->filterPosition === self::FILTER_POS_FOOTER) {
            $content .= $this->renderFilters();
        }
        return $this->renderTablePart('tfoot', $content);
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
        $configItems = [
            'item' => $this->itemLabelSingle,
            'items' => $this->itemLabelPlural,
            'items-few' => $this->itemLabelFew,
            'items-many' => $this->itemLabelMany,
            'items-acc' => $this->itemLabelAccusative,
        ];
        $pagination = $this->dataProvider->getPagination();
        if ($pagination !== false) {
            $totalCount = $this->dataProvider->getTotalCount();
            $begin = $pagination->getPage() * $pagination->pageSize + 1;
            $end = $begin + $count - 1;
            if ($begin > $end) {
                $begin = $end;
            }
            $page = $pagination->getPage() + 1;
            $pageCount = $pagination->pageCount;
            $configSummary = [
                'begin' => $begin,
                'end' => $end,
                'count' => $count,
                'totalCount' => $totalCount,
                'page' => $page,
                'pageCount' => $pageCount,
            ];
            if (($summaryContent = $this->summary) === null) {
                return Html::tag($tag, Yii::t('kvgrid',
                    'Showing <b>{begin, number}-{end, number}</b> of <b>{totalCount, number}</b> {totalCount, plural, one{{item}} other{{items}}}.',
                    $configSummary + $configItems
                ), $summaryOptions);
            }
        } else {
            $begin = $page = $pageCount = 1;
            $end = $totalCount = $count;
            $configSummary = [
                'begin' => $begin,
                'end' => $end,
                'count' => $count,
                'totalCount' => $totalCount,
                'page' => $page,
                'pageCount' => $pageCount,
            ];
            if (($summaryContent = $this->summary) === null) {
                return Html::tag($tag,
                    Yii::t('kvgrid', 'Total <b>{count, number}</b> {count, plural, one{{item}} other{{items}}}.',
                        $configSummary + $configItems
                    ), $summaryOptions);
            }
        }

        return Yii::$app->getI18n()->format($summaryContent, $configSummary, Yii::$app->language);
    }
}
