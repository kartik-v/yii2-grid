<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2022
 * @version   3.5.0
 */

namespace kartik\grid;

use yii\helpers\ArrayHelper;

/**
 * The [[EnumColumn]] allows you to configure and display a dynamic content / markup for each of the cell attribute
 * values based on enumerated `$value => $content` pairs. An example of the usage:
 *
 * ```php
 * // Example 1
 * [
 *      'class' => 'kartik\grid\EnumColumn',
 *      'attribute' => 'role',
 *      'enum' => User::getRoles(),
 *      'loadEnumAsFilter' => true, // optional - defaults to `true`
 * ],
 * // Example 2
 * [
 *      'class' => 'kartik\grid\EnumColumn',
 *      'attribute' => 'gender',
 *      'enum' => [
 *          '0' => '<span class="text-muted">Unknown</span>',
 *          'F' => '<span class="text-success">Female</span>',
 *          'M' => '<span class="text-danger">Male</span>',
 *      ],
 *      'filter' => [  // will override the grid column filter
 *          '0' => 'Unknown',
 *          'F' => 'Female',
 *          'M' => 'Male',
 *      ],
 * ]
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since  1.0
 */
class EnumColumn extends DataColumn
{
    /**
     * @var array the `$value => $content` pairs that will be used for conversion of the attribute values to your own
     * predefined markup. The `$content` markup will not be HTML coded. If [[loadEnumAsFilter]] is set to `true`, and
     * `filter` property is not set, then the `filter` property will automatically default to this property's value.
     */
    public $enum = [];

    /**
     * @var bool whether to automatically set the `filter` property to the `enum` property value, if `filter` property
     * is not set
     */
    public $loadEnumAsFilter = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->loadEnumAsFilter && !isset($this->filter)) {
            $this->filter = $this->enum;
        }
    }

    /**
     * @inheritdoc
     */
    public function getDataCellValue($model, $key, $index)
    {
        $value = parent::getDataCellValue($model, $key, $index);
        return $value === null ? null : ArrayHelper::getValue($this->enum, $value, $value);
    }
}