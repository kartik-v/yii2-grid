yii2-grid
=========

Yii's amazing GridView on steroids. Various modifications and enhancements to the GridView widget along with additionally enhanced Grid Columns for Yii Framework 2.0.
The widget contains other enhancements to use various Bootstrap 3.x styling functionalities.

## GridView
The GridView widget contains these parameters as modifications and enhancements.

### Table Style Togglers

- `bootstrap`:  _boolean_ whether the grid view will have a Bootstrap table styling. Defaults to `true`. If set
   to `false`, will automatically disable/remove all Bootstrap specific markup from the grid table and filters.
- `bordered`: _boolean_ whether the grid table will have a `bordered` style. Applicable only if `bootstrap` is `true`. Defaults to `true`.
- `striped`: _boolean_ whether the grid table will have a `striped` style. Applicable only if `bootstrap` is `true`. Defaults to `true`.
- `condensed`: _boolean_ whether the grid table will have a `condensed` style. Applicable only if `bootstrap` is `true`. Defaults to `false`.
- `responsive`: _boolean_ whether the grid table will have a `responsive` style. Applicable only if `bootstrap` is `true`. Defaults to `true`.
- `hover`: _boolean_ whether the grid table will highlight row on `hover`. Applicable only if `bootstrap` is `true`. Defaults to `false`.
- `tableOptions`: _array_ HTML attributes for the grid table element. This is auto generated based on the above settings.
- `footerRowOptions`: _array_ HTML attributes for the table footer row. Defaults to `['class' => 'kv-table-footer']`
- `captionOptions`: _array_ HTML attributes for the table caption. Defaults to `['class' => 'kv-table-caption']`

### Floating Header

- `floatHeader`: _boolean_ whether the grid table will have a floating table header. Uses the [JQuery Float THead plugin](http://mkoryak.github.io/floatThead) to display a seamless
   floating table header. The floating behavior will only be applied if `filterPostion` is set to [[GridView::FILTER_POS_HEADER]] or [[GridView::FILTER_POS_FOOTER]].
   Defaults to `false`.
- `floatHeaderOptions`: _array_ the plugin options for the [floatThead plugin](http://mkoryak.github.io/floatThead#options) that would render the floating/sticky table header behavior.
   The default offset from the top of the window where the floating header will 'stick' when scrolling down is set to `50` assuming a fixed
   bootstrap navbar on top. You can set this to 0 or any javascript function/expression. Defaults to `['scrollingTop' => 50]`.

### Panel

- `panel`: _array_ the panel settings. If this is set, the grid widget will be embedded in a Bootstrap panel. Applicable only if
  `bootstrap` is `true`. The following array keys are supported:
   - `heading`: _string_, the panel heading. If not set, will not be displayed.
   - `type`: _string_, the Bootstrap contextual color type. Should be one of the GridView TYPE constants below. If not set will default to `default` or `self::TYPE_DEFAULT`.
	  * GridView::TYPE_DEFAULT or 'default'
	  * GridView::TYPE_PRIMARY or 'primary'
	  * GridView::TYPE_INFO or 'info'
	  * GridView::TYPE_DANGER or 'danger';
	  * GridView::TYPE_WARNING or 'warning'
	  * GridView::TYPE_SUCCESS or 'success'
   - `footer`: _string_, the panel footer. If not set, will not be displayed.
   - `before`: _string_, the panel content to be placed before/above the grid table (after the header).
   - `beforeOptions`: _array_, HTML attributes for the `before` text. If the `class` is not set, it will default to `kv-panel-before`.
   - `after`: _string_, the panel content to be placed after/above the grid table (before the footer).
   - `afterOptions`: _array_, HTML attributes for the after` text. If the `class` is not set, it will default to `kv-panel-after`.
   - `showFooter`: _boolean_, whether to always show the footer. If so the, grid layout will default to `GridView::TEMPLATE_1`. If this is
	 set to `false`, the `pager` will be enclosed within the `kv-panel-after` container. Defaults to `false`.
   - `layout`: _string_, the grid layout to be used if you are using a panel. If not set and `showFooter` is `true, will default to `GridView::TEMPLATE_1`.
	  If not set and `showFooter` is set to `false`, this will default to `GridView::TEMPLATE_2`.

### Page Summary
This is a new feature added to the GridView widget. The page summary is an additional row above the footer - for displaying the
summary/totals for the current GridView page. The following parameters are applicable to control this behavior:

- `showPageSummary`: _boolean_ whether to display the page summary row for the grid view. Defaults to `false`.
- `pageSummaryRowOptions`:  _array_, HTML attributes for the page summary row. Defaults to `['class' => 'kv-page-summary warning']`.

### Data Column (Enhanced)
The default Yii data column has been enhanced with these following parameters:

- `hAlign`: _string_ the horizontal alignment of the column. This will automatically set the header, body, footer, and page summary to this alignment.
  Should be one of GridView ALIGN constants below.
  * GridView::ALIGN_RIGHT or 'right'
  * GridView::ALIGN_CENTER or 'center'
  * GridView::ALIGN_LEFT or 'left'

- `vAlign`: _string_ the vertical alignment of the column. This will automatically set the header, body, footer, and page summary to this alignment.
  Should be one of GridView ALIGN constants below.
  * GridView::ALIGN_TOP or 'top'
  * GridView::ALIGN_MIDDLE or 'middle'
  * GridView::ALIGN_BOTTOM or 'bottom'

- `width`: _string_ the width of each column - matches the [CSS width property](http://www.w3schools.com/cssref/pr_dim_width.asp). This will automatically
  set the header, body, footer, and page summary to this value.

- `filterType`: _string_ the filter input type for each column. This allows you to set a filter input type other than the default text or dropdown list.
   You can pass in any widget classname extending from the Yii Input Widget. In most cases, you can use one of predefined kartik\widgets from the
   GridView FILTER constants below:
	- \kartik\widgets
		* FILTER_SELECT2 or '\kartik\widgets\Select2';
		* FILTER_TYPEAHEAD or '\kartik\widgets\Typeahead';
		* FILTER_SWITCH or '\kartik\widgets\Switch';
		* FILTER_SPIN or '\kartik\widgets\TouchSpin';
		* FILTER_STAR or '\kartik\widgets\StarRating';
		* FILTER_DATE or '\kartik\widgets\DatePicker';
		* FILTER_TIME or '\kartik\widgets\TimePicker';
		* FILTER_RANGE or '\kartik\widgets\RangeInput';
		* FILTER_COLOR or '\kartik\widgets\ColorInput';
    - other filter input types
		* FILTER_CHECKBOX or 'checkbox';
		* FILTER_RADIO or 'radio';

- `filterWidgetOptions`: _array_ the options/settings for the filter widget. Will be used only if you set `filterType` to a widget classname that exists.

- `mergeHeader`: _boolean_ whether to merge the header title row and the filter row. This is useful when you do not have a filter applicable for the column
   (e.g.the ActionColumn or the SerialColumn). This will not render the filter for the column and can be used when `filter` is set to `false`.
   Defaults to `false`.
   > NOTE: The merging will be done only when `filterPosition` for the grid is set to FILTER_POS_BODY.

- `pageSummary`: _boolean|string|Closure_ the page summary that is displayed above the footer. You can
   set it to one of the following:
	 * `false`: the summary will not be displayed.
	 * `true`: the page summary for the column will be calculated and displayed using the `summaryFunc` setting.
	 * any `string`: will be displayed as is
	 * `Closure`: you can set it to an anonymous function with the following signature:
	 ```
	 // example 1
	 function ($summary, $data, $widget) { return 'Count is ' . $summary; }
	 // example 2
	 function ($summary, $data, $widget) { return 'Range ' . min($data) . ' to ' . max($data); }
	 ```
	 where
	 * the `$summary` variable will be replaced with the calculated summary using the `summaryFunc` setting.
	 * the `$data` variable will contain array of the selected page rows for the column.