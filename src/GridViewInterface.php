<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2021
 * @version   3.5.0
 */

namespace kartik\grid;

/**
 * The Krajee GridView interface
 *
 * @see http://demos.krajee.com/grid
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since  1.0
 */
interface GridViewInterface
{
    /**
     * @var string the top part of the table after the header (used for location of the page summary row)
     */
    const POS_TOP = 'top';

    /**
     * @var string the bottom part of the table before the footer (used for location of the page summary row)
     */
    const POS_BOTTOM = 'bottom';

    /**
     * @var string the **default** bootstrap contextual color type (applicable only for panel contextual style)
     */
    const TYPE_DEFAULT = 'default';

    /**
     * @var string the **light** bootstrap contextual color type (applicable only for panel contextual style)
     */
    const TYPE_LIGHT = 'light';

    /**
     * @var string the **dark** bootstrap contextual color type (applicable only for panel contextual style)
     */
    const TYPE_DARK = 'dark';

    /**
     * @var string the **secondary** bootstrap contextual color type
     */
    const TYPE_SECONDARY = 'secondary';

    /**
     * @var string the **primary** bootstrap contextual color type
     */
    const TYPE_PRIMARY = 'primary';

    /**
     * @var string the **information** bootstrap contextual color type
     */
    const TYPE_INFO = 'info';

    /**
     * @var string the **danger** bootstrap contextual color type
     */
    const TYPE_DANGER = 'danger';

    /**
     * @var string the **warning** bootstrap contextual color type
     */
    const TYPE_WARNING = 'warning';

    /**
     * @var string the **success** bootstrap contextual color type
     */
    const TYPE_SUCCESS = 'success';

    /**
     * @var string the **active** bootstrap contextual color type (applicable only for table row contextual style)
     */
    const TYPE_ACTIVE = 'active';

    /**
     * @var string the Bootstrap 3.x **active** icon markup for [[BooleanColumn]]
     */
    const ICON_ACTIVE = '<span class="glyphicon glyphicon-ok-sign text-success" style="font-weight:bold"></span>';

    /**
     * @var string the **inactive** icon markup for [[BooleanColumn]]
     */
    const ICON_INACTIVE = '<span class="glyphicon glyphicon-remove-sign text-danger" style="font-weight:bold"></span>';

    /**
     * @var string the Bootstrap 3.x **expanded** icon markup for [[ExpandRowColumn]]
     */
    const ICON_EXPAND = '<span class="glyphicon glyphicon-expand"></span>';

    /**
     * @var string the Bootstrap 3.x **collapsed** icon markup for [[ExpandRowColumn]]
     */
    const ICON_COLLAPSE = '<span class="glyphicon glyphicon-collapse-down"></span>';

    /**
     * @var string the Bootstrap 4.x / 5.x **active** icon markup for [[BooleanColumn]]
     */
    const ICON_ACTIVE_BS4 = '<span class="fas fa-check-circle text-success"></span>';

    /**
     * @var string the Bootstrap 4.x / 5.x **inactive** icon markup for [[BooleanColumn]]
     */
    const ICON_INACTIVE_BS4 = '<span class="fas fa-times-circle text-danger"></span>';

    /**
     * @var string the Bootstrap 4.x / 5.x **expanded** icon markup for [[ExpandRowColumn]]
     */
    const ICON_EXPAND_BS4 = '<span class="far fa-plus-square"></span>';

    /**
     * @var string the Bootstrap 4.x / 5.x **collapsed** icon markup for [[ExpandRowColumn]]
     */
    const ICON_COLLAPSE_BS4 = '<span class="far fa-minus-square"></span>';

    /**
     * @var string the status for a **default** row in [[ExpandRowColumn]]
     */
    const ROW_NONE = -1;

    /**
     * @var string the status for an **expanded** row in [[ExpandRowColumn]]
     */
    const ROW_EXPANDED = 0;

    /**
     * @var string the status for a **collapsed** row in [[ExpandRowColumn]]
     */
    const ROW_COLLAPSED = 1;
    /**
     * @var string horizontal **right** alignment for grid cells
     */
    const ALIGN_RIGHT = 'right';

    /**
     * @var string horizontal **center** alignment for grid cells
     */
    const ALIGN_CENTER = 'center';

    /**
     * @var string horizontal **left** alignment for grid cells
     */
    const ALIGN_LEFT = 'left';

    /**
     * @var string vertical **top** alignment for grid cells
     */
    const ALIGN_TOP = 'top';

    /**
     * @var string vertical **middle** alignment for grid cells
     */
    const ALIGN_MIDDLE = 'middle';

    /**
     * @var string vertical **bottom** alignment for grid cells
     */
    const ALIGN_BOTTOM = 'bottom';

    /**
     * @var string CSS to apply to prevent wrapping of grid cell data
     */
    const NOWRAP = 'kv-nowrap';

    /**
     * @var string grid filter input type for [[Html::checkbox]]
     */
    const FILTER_CHECKBOX = 'checkbox';

    /**
     * @var string grid filter input type for [[Html::radio]]
     */
    const FILTER_RADIO = 'radio';

    /**
     * @var string grid filter input type for [[\kartik\select2\Select2]] widget
     */
    const FILTER_SELECT2 = '\kartik\select2\Select2';

    /**
     * @var string grid filter input type for [[\kartik\typeahead\Typeahead]] widget
     */
    const FILTER_TYPEAHEAD = '\kartik\typeahead\Typeahead';

    /**
     * @var string grid filter input type for [[\kartik\switchinput\SwitchInput]] widget
     */
    const FILTER_SWITCH = '\kartik\switchinput\SwitchInput';

    /**
     * @var string grid filter input type for [[\kartik\touchspin\TouchSpin]] widget
     */
    const FILTER_SPIN = '\kartik\touchspin\TouchSpin';

    /**
     * @var string grid filter input type for [[\kartik\rating\StarRating]] widget
     */
    const FILTER_STAR = '\kartik\rating\StarRating';

    /**
     * @var string grid filter input type for [[\kartik\date\DatePicker]] widget
     */
    const FILTER_DATE = '\kartik\date\DatePicker';

    /**
     * @var string grid filter input type for [[\kartik\time\TimePicker]] widget
     */
    const FILTER_TIME = '\kartik\time\TimePicker';

    /**
     * @var string grid filter input type for [[\kartik\datetime\DateTimePicker]] widget
     */
    const FILTER_DATETIME = '\kartik\datetime\DateTimePicker';

    /**
     * @var string grid filter input type for [[\kartik\daterange\DateRangePicker]] widget
     */
    const FILTER_DATE_RANGE = '\kartik\daterange\DateRangePicker';

    /**
     * @var string grid filter input type for [[\kartik\sortinput\SortableInput]] widget
     */
    const FILTER_SORTABLE = '\kartik\sortinput\SortableInput';

    /**
     * @var string grid filter input type for [[\kartik\range\RangeInput]] widget
     */
    const FILTER_RANGE = '\kartik\range\RangeInput';

    /**
     * @var string grid filter input type for [[\kartik\color\ColorInput]] widget
     */
    const FILTER_COLOR = '\kartik\color\ColorInput';

    /**
     * @var string grid filter input type for [[\kartik\slider\Slider]] widget
     */
    const FILTER_SLIDER = '\kartik\slider\Slider';

    /**
     * @var string grid filter input type for [[\kartik\money\MaskMoney]] widget
     */
    const FILTER_MONEY = '\kartik\money\MaskMoney';

    /**
     * @var string grid filter input type for [[\kartik\number\NumberControl]] widget
     */
    const FILTER_NUMBER = '\kartik\number\NumberControl';

    /**
     * @var string grid filter input type for [[\kartik\checkbox\CheckboxX]] widget
     */
    const FILTER_CHECKBOX_X = '\kartik\checkbox\CheckboxX';

    /**
     * @var string identifier for the `COUNT` summary function
     */
    const F_COUNT = 'f_count';

    /**
     * @var string identifier for the `SUM` summary function
     */
    const F_SUM = 'f_sum';
    /**
     * @var string identifier for the `MAX` summary function
     */
    const F_MAX = 'f_max';

    /**
     * @var string identifier for the `MIN` summary function
     */
    const F_MIN = 'f_min';

    /**
     * @var string identifier for the `AVG` summary function
     */
    const F_AVG = 'f_avg';

    /**
     * @var string HTML (Hyper Text Markup Language) export format
     */
    const HTML = 'html';

    /**
     * @var string CSV (comma separated values) export format
     */
    const CSV = 'csv';

    /**
     * @var string Text export format
     */
    const TEXT = 'txt';

    /**
     * @var string Microsoft Excel 95+ export format
     */
    const EXCEL = 'xls';

    /**
     * @var string PDF (Portable Document Format) export format
     */
    const PDF = 'pdf';

    /**
     * @var string JSON (Javascript Object Notation) export format
     */
    const JSON = 'json';

    /**
     * @var string set download target for grid export to a popup browser window
     */
    const TARGET_POPUP = '_popup';

    /**
     * @var string set download target for grid export to the same open document on the browser
     */
    const TARGET_SELF = '_self';

    /**
     * @var string set download target for grid export to a new window that auto closes after download
     */
    const TARGET_BLANK = '_blank';
}
