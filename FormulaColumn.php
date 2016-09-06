<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2016
 * @version   3.1.3
 */

namespace kartik\grid;

use yii\base\InvalidConfigException;

/**
 * A FormulaColumn to calculate values based on other column indexes for the Grid widget [[\kartik\grid\GridView]].
 * This extends and builds upon the [[DataColumn]] in the [[GridView]] widget.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class FormulaColumn extends DataColumn
{
    const SUMMARY = -10000;
    const FOOTER = -20000;

    /**
     * @var boolean automatically generate the footer. If set to `true`, it will use the same formula to generate the footer. If set to `false`, will use the default footer.
     */
    public $autoFooter = true;

    /**
     * Gets the value of a column
     *
     * @param integer $i the index of the grid column (the first column in the grid will be zero indexed). Note a
     * column's index is to be considered, even if the `visible` property is set to false.
     * @param array   $params which will contain these keys:
     * - `model`: _yii\base\Model`, the data model being rendered
     * - `key`: _string|object_, the key associated with the data model
     * - `index`: _integer_, the zero-based index of the data item among the item array returned by
     *   [[GridView::dataProvider]].
     * - widget: _FormulaColumn_, the current column widget instance
     *
     * @return string
     * @throws InvalidConfigException
     */
    public function col($i, $params = [])
    {
        if (empty($this->grid->columns[$i])) {
            throw new InvalidConfigException("Invalid column index {$i} used in FormulaColumn.");
        }
        if (!isset($this->value) || !$this->value instanceof \Closure) {
            throw new InvalidConfigException(
                "The 'value' must be set and defined as a `Closure` function for a FormulaColumn."
            );
        }
        /** @var DataColumn $col */
        $col = $this->grid->columns[$i];
        if ($col === $this) {
            throw new InvalidConfigException("Self-referencing FormulaColumn at column {$i}.");
        }
        $model = null;
        $key = null;
        $index = null;
        extract($params);
        if ($index == self::SUMMARY) {
            return $col->getPageSummaryCellContent();
        } elseif ($index == self::FOOTER) {
            return $col->getFooterCellContent();
        } else {
            return $col->getDataCellValue($model, $key, $index);
        }
    }

    /**
     * Get the raw footer cell content.
     *
     * @return string the rendering result
     */
    protected function getFooterCellContent()
    {
        if ($this->autoFooter) {
            return call_user_func($this->value, null, self::FOOTER, self::FOOTER, $this);
        }
        return parent::getFooterCellContent();
    }

    /**
     * Formatted footer cell content.
     *
     * @return string the rendering result
     */
    protected function renderFooterCellContent()
    {
        if ($this->autoFooter) {
            return $this->grid->formatter->format($this->getFooterCellContent(), $this->format);
        }
        return parent::renderFooterCellContent();
    }
}
