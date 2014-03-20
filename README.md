yii2-grid
=========

Yii2 GridView on steroids. Various modifications and enhancements to the amazing Yii GridView widget. Contains new additional Grid Columns with enhanced settings for Yii Framework 2.0.
The widget also incorporates various Bootstrap 3.x styling options. Refer [documentation here](https://github.com/kartik-v/yii2-grid/blob/master/DOC.md).

## GridView
### \kartik\grid\GridView
The GridView widget contains these parameters as modifications and enhancements.

### Table Styling (Enhanced)
Control various options to style your grid table.
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

### Floating Header (New)
Allows the grid table to have a floating table header. Uses the [JQuery Float THead plugin](http://mkoryak.github.io/floatThead) to display a seamless floating table header. 

### Panel (New)
Allows configuration of GridView to be enclosed in a panel that can be styled as per  Bootstrap 3.x. The panel will enable configuration of  various 
sections to embed content/buttons, before and after header, and before and after footer.

### Page Summary (New)
This is a new feature added to the GridView widget. The page summary is an additional row above the footer - for displaying the
summary/totals for the current GridView page. The following parameters are applicable to control this behavior:

- `showPageSummary`: _boolean_ whether to display the page summary row for the grid view. Defaults to `false`.
- `pageSummaryRowOptions`:  _array_, HTML attributes for the page summary row. Defaults to `['class' => 'kv-page-summary warning']`.

## Data Column (Enhanced)
### \kartik\grid\DataColumn
The default Yii data column has been enhanced with various additional parameters.

## Formula Column (New)
### \kartik\grid\FormulaColumn
This is a new grid column class that extends the \kartik\grid\DataColumn class. It allows calculating formulae just like in spreadsheets - based on
values of other columns in the grid. The formula calculation is done at grid rendering runtime and does not need to query the database. Hence you can use formula columns
within another formula column. 

## Action Column (Enhanced)
### \kartik\grid\ActionColumn
Enhancements of `\yii\grid\ActionColumn` to work with the new pageSummary and a default styling to work for many scenarios. 

## Serial Column (Enhanced)
### \kartik\grid\SerialColumn
Enhancement of `\yii\grid\SerialColumn` to work with the new pageSummary and a default styling to work for many scenarios. 

## Checkbox Column (Enhanced)
### \kartik\grid\CheckboxColumn
Enhancements of `\yii\grid\CheckboxColumn` to work with the new pageSummary and a default styling to work for many scenarios. 


### Demo
You can see detailed [documentation](http://demos.krajee.com/grid) and [demonstration](http://demos.krajee.com/grid-demo) on usage of the extension.

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
$ php composer.phar require kartik-v/yii2-grid "dev-master"
```

or add

```
"kartik-v/yii2-grid": "dev-master"
```

to the ```require``` section of your `composer.json` file.

## Usage
```php
use kartik\widgets\GridView;
```

## License

**yii2-grid** is released under the BSD 3-Clause License. See the bundled `LICENSE.md` for details.
