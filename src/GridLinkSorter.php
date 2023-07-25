<?php
/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2023
 * @version   3.5.3
 */

namespace kartik\grid;

use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\widgets\LinkSorter;

/**
 * GridLinkSorter extends LinkSorter to Krajee specific sort icons and renders a list of sort links for the
 * given sort definition.
 *
 * LinkSorter will generate a hyperlink for every attribute declared in [[sort]].
 *
 * For more details and usage information on LinkSorter, see the [guide article on sorting](guide:output-sorting).
 *
 * @see http://demos.krajee.com/grid
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since  1.0
 */
class GridLinkSorter extends LinkSorter
{
    /**
     * @var array the configuration for sorter icons (parsed only when using with Bootstrap 4.x and above)
     * The array key must have an SORT_ASC and SORT_DESC entry.  The `sorterIcons` property defaults to
     * following if not overridden:
     * [
     *   SORT_ASC => '<i class="fas fa-sort-amount-down-alt"></i>',
     *   SORT_DESC => '<i class="fas fa-sort-amount-up"></i>'
     * ]
     */
    public $sorterIcons = [];

    /**
     * Renders the sort links.
     * @return string the rendering result
     * @throws InvalidConfigException
     */
    protected function renderSortLinks()
    {
        $this->sorterIcons += GridView::getDefaultSorterIcons(false);
        $attributes = empty($this->attributes) ? array_keys($this->sort->attributes) : $this->attributes;
        $links = [];
        $sort = $this->sort;
        foreach ($attributes as $name) {
            $icon = '';
            if (($direction = $sort->getAttributeOrder($name)) !== null) {
                $icon = Html::tag('span', $this->sorterIcons[$direction], ['class' => 'kv-sort-icon']);
            }
            $options = $this->linkOptions;
            if (isset($sort->attributes[$name]['label'])) {
                $label = $sort->attributes[$name]['label'];
            } else {
                $label = Inflector::camel2words($name);
            }
            Html::addCssClass($options, 'kv-sort-link');
            $options['label'] = $label.$icon;
            $links[] = $sort->link($name, $options);
        }

        return Html::ul($links, array_merge($this->options, ['encode' => false]));
    }
}
