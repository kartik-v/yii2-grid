yii2-grid
=========

[![Latest Stable Version](https://poser.pugx.org/kartik-v/yii2-grid/v/stable)](https://packagist.org/packages/kartik-v/yii2-grid)
[![Latest Unstable Version](https://poser.pugx.org/kartik-v/yii2-grid/v/unstable)](https://packagist.org/packages/kartik-v/yii2-grid)
[![License](https://poser.pugx.org/kartik-v/yii2-grid/license)](https://packagist.org/packages/kartik-v/yii2-grid)
[![Total Downloads](https://poser.pugx.org/kartik-v/yii2-grid/downloads)](https://packagist.org/packages/kartik-v/yii2-grid)
[![Monthly Downloads](https://poser.pugx.org/kartik-v/yii2-grid/d/monthly)](https://packagist.org/packages/kartik-v/yii2-grid)
[![Daily Downloads](https://poser.pugx.org/kartik-v/yii2-grid/d/daily)](https://packagist.org/packages/kartik-v/yii2-grid)

Yii2 GridView on steroids. A module with various modifications and enhancements to one of the most used widgets by Yii developers. The widget contains new additional Grid Columns with enhanced settings for Yii Framework 2.0. The widget also incorporates various Bootstrap 3.x styling options.
Refer [detailed documentation](http://demos.krajee.com/grid) and/or a [complete demo](http://demos.krajee.com/grid-demo). You can also view the [grid grouping demo here](http://demos.krajee.com/group-grid).

![GridView Screenshot](https://lh4.googleusercontent.com/-4x-CdyyZAsY/VNxLPmaaAXI/AAAAAAAAAQ8/XYYxTiQZvJk/w868-h516-no/krajee-yii2-grid.jpg)

## Latest Release
The latest version of the module is v3.1.3. Refer the [CHANGE LOG](https://github.com/kartik-v/yii2-grid/blob/master/CHANGE.md) for details. 

New features with release 2.7.0.

1. A brand new column `ExpandRowColumn` has been added which allows one to expand grid rows, show details, or load content via ajax. Check the [ExpandRowColumn documentation](http://demos.krajee.com/grid#expand-row-column) for further details. The features available with this column are:
    - Ability to expand grid rows and show a detail content in a new row below it like a master detail record. 
    - Allows configuring the column like any grid DataColumn. The value of the column determines if the row is to be expanded or collapsed by default.
    - Allows you to configure/customize the expand and collapse indicators.
    - Ability to configure only specific rows to have expand/collapse functionality.
    - Ability to disable the expand / collapse behavior and indicators for selective rows.
    - Allows you to configure the detail content markup directly in the column configuration (using `detail` property). This can be set as a HTML markup directly or via Closure callback using column parameters.
    - Allows you to load the detail content markup via ajax. Set the `detailUrl` property directly or via a Closure callback using column parameters.
    - Automatically caches the content loaded via ajax so that the content is rendered from local on toggling the expand/collapse indicators, until the grid state is changed via filtering, sorting, or pagination.
    - Ability to batch expand or batch collapse grid rows from the header. If content is loaded via ajax, the batch expand and collapse will fire the ajax requests to load and use intelligently from cache where possible.
2. Included `prepend` and `append` settings within `pageSummaryOptions` to prepend/append content to page summary.
3. All asset (JS & CSS) files have been carefully isolated to only load them if the specific grid feature has been selected.
4. Enhancements for JS confirmation popups being hidden by browser's hide dialog settings.
5. Recursively replace/merge PDF export configuration correctly.
6. Include demo messages for auto generating via config.
7. Allows grouping grid column data, including master detail groups and generating group summaries (since v3.0.5).
8. Allows special formatting of data for cells exported in Excel Format.

> NOTE: This extension depends on other yii2 extensions based on the functionality chosen by you. It will not install such dependent packages by default, but will prompt through an exception, if accessed.
For example, if you choose to enable PDF export, then the [yii2-mpdf](http://demos.krajee.com/mpdf) will be mandatory and exception will be raised if `yii2-mpdf` is not installed.
Check the [composer.json](https://github.com/kartik-v/yii2-grid/blob/master/composer.json) for other extension dependencies.

## Module
The extension has been created as a module to enable access to advanced features like download actions (exporting as csv, text, html, or xls). You should configure the module with a name of `gridview` as shown below:
```php
'modules' => [
   'gridview' =>  [
        'class' => '\kartik\grid\Module'
    ]
],
```

## GridView
### \kartik\grid\GridView
The following functionalities have been added/enhanced:

### Table Styling (Enhanced)
Control various options to style your grid table. Added `containerOptions` to customize your grid table container. Enhancements for grid and columns to work with yii\widgets\Pjax.

### Grid Grouping (New)
With release v3.0.5, the module allows grouping of GridView data by setting various `group` related properties at the `kartik\grid\DataColumn` level. The following functionalities are supported:

- Ability to group and merge similar data for each column.
- Allow multi level/complex grouping and making a sub group dependent on a parent group.
- Allow displaying grouped data as a separate grouped row above the child rows.
- Allow configuring and displaying of group level summary rows.
- Summaries can be setup as a group footer OR a group header.
- Summaries intelligently embed between sub-groups and parent groups.
- Summaries can include auto calculated values (for numbers) at runtime based on previous child column data.
- Summaries can include advanced calculations using a javascript callback configuration.
- Ability to merge columns in the summary group header or footer.
- Complex configurations of groups will allow -  group properties to be set dynamically using Closure.
- Allow you to style your group cells in various ways including setting odd and even row CSS properties.

### Pjax Settings (New)
Inbuilt support for Pjax. Enhancements for grid and columns to work with `yii\widgets\Pjax`. Auto-reinitializes embedded javascript plugins when GridView is refreshed via Pjax. Added `pjax` property to enable pjax and `pjaxSettings` to customize the pjax behavior.

### Custom Header & Footer (New)
Add custom header or footer rows, above / below your default grid header and footer.

### Resizing Columns (New)
Allows resizing of the columns just like a spreadsheet (since v3.0.0). Uses the [JQuery ResizableColumns plugin](https://github.com/dobtco/jquery-resizable-columns) for resize and [store.js](https://github.com/marcuswestin/store.js/) for localStorage persistence.

### Floating Header (New)
Allows the grid table to have a floating table header. Uses the [JQuery Float THead plugin](http://mkoryak.github.io/floatThead) to display a seamless floating table header. 

### Panel (New)
Allows configuration of GridView to be enclosed in a panel that can be styled as per  Bootstrap 3.x. The panel will enable configuration of  various
sections to embed content/buttons, before and after header, and before and after footer.

### Toolbar (New)
The grid offers ability to configure toolbar for adding various actions. The default templates place the toolbar in the `before` section of the `panel`. The toolbar is by default styled using Bootstrap button groups. Some of the default actions like the `export` button is by default appended to the toolbar. 
With version v2.1.0, if you are using the `yii2-dynagrid` extension it automatically displays the  **personalize**, **sort**, and **filter** buttons in the toolbar. The toolbar can be configured as a simple array. Refer the [docs and demos](http://demos.krajee.com/grid) for details.

### Grid Plugins (New)
The grid now offers ability to plugin dynamic content to your grid at runtime. A new property `replaceTags` has been added with v2.3.0. This allows you to specify tags which will be replaced dynamically at grid rendering time and wherever you set these tags in any of the grid layout templates.

### Page Summary (New)
This is a new feature added to the GridView widget. The page summary is an additional row above the footer - for displaying the
summary/totals for the current GridView page. The following parameters are applicable to control this behavior:

- `showPageSummary`: _boolean_ whether to display the page summary row for the grid view. Defaults to `false`.
- `pageSummaryRowOptions`:  _array_, HTML attributes for the page summary row. Defaults to `['class' => 'kv-page-summary warning']`.

### Export Grid Data (New)
This is a new feature added to the GridView widget. It allows you to export the displayed grid content as HTML, CSV, TEXT, EXCEL, PDF, & JSON. It uses the rendered grid data on client to convert to one of the format specified using JQuery. 
This is supported across all browsers. The PDF rendering is achieved through a separate extension [yii2-mpdf](http://demos.krajee.com/mpdf).

Features offered by yii2-grid export:

- Ability to preprocess and convert column data to your desired value before exporting. There is a new property `exportConversions` that can be setup in GridView. 
For example, this currently is set as a default to convert the HTML formatted icons for BooleanColumn to user friendly text like `Active` or `Inactive` after export.
- Hide any row or column in the grid by adding one or more of the following CSS classes:
    - `skip-export`: Will skip this element during export for all formats (`html`, `csv`, `txt`, `xls`, `pdf`, `json`).
    - `skip-export-html`: Will skip this element during export only for `html` export format.
    - `skip-export-csv`: Will skip this element during export only for `csv` export format.
    - `skip-export-txt`: Will skip this element during export only for `txt` export format.
    - `skip-export-xls`: Will skip this element during export only for `xls` (excel) export format.
    - `skip-export-pdf`: Will skip this element during export only for `pdf` export format.
    - `skip-export-json`: Will skip this element during export only for `json` export format.
    These CSS can be set virtually anywhere. For example `headerOptions`, `contentOptions`, `beforeHeader` etc.
- With release v2.1.0, you can now merge additional action items to the export button dropdown.
- With release v2.3.0 the export functionality includes these additional features:
    - A separate export popup progress window is now shown for download. 
    - Asynchronous export process on the separate window - and avoid any grid refresh
    - Set export mime types to be configurable
    - Includes support for exporting new file types:
        - JSON export 
        - PDF export (using `yii2-mpdf` extension)
    - Adds functionality for full data export
    - Enhance icons formatting for export file types (and beautify optionally using font awesome)
    - Ability to hide entire column from export using `hiddenFromExport` property, but show them in normal on screen display.
    - Ability to do reverse of above. Hide column in display but show on export using `hidden` property.
- Adds ability to integrate a separate extension for full data export i.e. [yii2-export](https://github.com/kartik-v/yii2-export).

### Toggle Grid Data (New)
This extension (with v2.3.0) adds ability to toggle between viewing **all grid data** and **paginated data**. By default the grid displays paginated data. This can be used for exporting complete grid data.

## Data Column (Enhanced)
### \kartik\grid\DataColumn
The default Yii data column has been enhanced with various additional parameters. Refer [documentation](http://demos.krajee.com/grid#data-column) for details.

## Expand Row Column (New)
### \kartik\grid\ExpandRowColumn
An enhanced data column that allows one to expand a grid row and display additional/detail content in a new row below it either directly or via ajax. Refer [documentation](http://demos.krajee.com/grid#expand-row-column) for details.

## Editable Column (New)
### \kartik\grid\EditableColumn
An enhanced data column that allows you to edit the cell content using [kartik\editable\Editable](http://demos.krajee.com/editable) widget. You can selectively choose to disable editable for certain rows or all rows. Refer [documentation](http://demos.krajee.com/grid#editable-column) for details.

## Formula Column (New)
### \kartik\grid\FormulaColumn
This is a new grid column class that extends the \kartik\grid\DataColumn class. It allows calculating formulae just like in spreadsheets - based on
values of other columns in the grid. The formula calculation is done at grid rendering runtime and does not need to query the database. Hence you can use formula columns
within another formula column. Refer [documentation](http://demos.krajee.com/grid#formula-column) for details.

## Boolean Column (New)
### \kartik\grid\BooleanColumn
This is a new grid column class that extends the \kartik\grid\DataColumn class. It automatically converts boolean data (true/false) values to user friendly indicators or labels (that are configurable). 
Refer [documentation](http://demos.krajee.com/grid#boolean-column) for details. The following are new features added since release v1.6.0:

- `BooleanColumn` icons have been setup as `ICON_ACTIVE` and `ICON_INACTIVE` constants in GridView.

## Radio Column (New)
### \kartik\grid\RadioColumn
This is a new grid column that works similar to the `CheckboxColumn`, but allows and restricts only a single row to be selected using radio inputs. In addition, it includes a header level clear button to clear the selected rows. It automatically works with the new pageSummary and includes a default styling to work for many scenarios. Refer [documentation](http://demos.krajee.com/grid#radio-column) for details.


## Action Column (Enhanced)
### \kartik\grid\ActionColumn
Enhancements of `\yii\grid\ActionColumn` to include optional dropdown Action menu and work with the new pageSummary and a default styling to work for many scenarios. Refer [documentation](http://demos.krajee.com/grid#action-column) for details.
The following are new features added since release v1.6.0:
- `ActionColumn` content by default has been disabled to appear in export output. The `skip-export` CSS class has been set as default in `headerOptions` and `contentOptions`.

## Serial Column (Enhanced)
### \kartik\grid\SerialColumn
Enhancement of `\yii\grid\SerialColumn` to work with the new pageSummary and a default styling to work for many scenarios. Refer [documentation](http://demos.krajee.com/grid#serial-column) for details.

## Checkbox Column (Enhanced)
### \kartik\grid\CheckboxColumn
Enhancements of `\yii\grid\CheckboxColumn` to work with the new pageSummary and a default styling to work for many scenarios. Refer [documentation](http://demos.krajee.com/grid#checkbox-column) for details.

### Demo
You can see detailed [documentation](http://demos.krajee.com/grid) and [demonstration](http://demos.krajee.com/grid-demo) on usage of the extension. You can also view the [grid grouping demo here](http://demos.krajee.com/group-grid).

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

### Pre-requisites
> Note: Check the [composer.json](https://github.com/kartik-v/yii2-dropdown-x/blob/master/composer.json) for this extension's requirements and dependencies. 
You must set the `minimum-stability` to `dev` in the **composer.json** file in your application root folder before installation of this extension OR
if your `minimum-stability` is set to any other value other than `dev`, then set the following in the require section of your composer.json file

```
kartik-v/yii2-grid: "@dev",
kartik-v/yii2-krajee-base: "@dev"
```

Read this [web tip /wiki](http://webtips.krajee.com/setting-composer-minimum-stability-application/) on setting the `minimum-stability` settings for your application's composer.json.

### Install

Either run

```
$ php composer.phar require kartik-v/yii2-grid "@dev"
```

or add

```
"kartik-v/yii2-grid": "@dev"
```

to the ```require``` section of your `composer.json` file.

## Usage
```php
use kartik\grid\GridView;
$gridColumns = [
    ['class' => 'kartik\grid\SerialColumn'],
    [
        'class' => 'kartik\grid\EditableColumn',
        'attribute' => 'name',
        'pageSummary' => 'Page Total',
        'vAlign'=>'middle',
        'headerOptions'=>['class'=>'kv-sticky-column'],
        'contentOptions'=>['class'=>'kv-sticky-column'],
        'editableOptions'=>['header'=>'Name', 'size'=>'md']
    ],
    [
        'attribute'=>'color',
        'value'=>function ($model, $key, $index, $widget) {
            return "<span class='badge' style='background-color: {$model->color}'> </span>  <code>" . 
                $model->color . '</code>';
        },
        'filterType'=>GridView::FILTER_COLOR,
        'vAlign'=>'middle',
        'format'=>'raw',
        'width'=>'150px',
        'noWrap'=>true
    ],
    [
        'class'=>'kartik\grid\BooleanColumn',
        'attribute'=>'status', 
        'vAlign'=>'middle',
    ],
    [
        'class' => 'kartik\grid\ActionColumn',
        'dropdown' => true,
        'vAlign'=>'middle',
        'urlCreator' => function($action, $model, $key, $index) { return '#'; },
        'viewOptions'=>['title'=>$viewMsg, 'data-toggle'=>'tooltip'],
        'updateOptions'=>['title'=>$updateMsg, 'data-toggle'=>'tooltip'],
        'deleteOptions'=>['title'=>$deleteMsg, 'data-toggle'=>'tooltip'], 
    ],
    ['class' => 'kartik\grid\CheckboxColumn']
];
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => $gridColumns,
    'containerOptions' => ['style'=>'overflow: auto'], // only set when $responsive = false
    'beforeHeader'=>[
        [
            'columns'=>[
                ['content'=>'Header Before 1', 'options'=>['colspan'=>4, 'class'=>'text-center warning']], 
                ['content'=>'Header Before 2', 'options'=>['colspan'=>4, 'class'=>'text-center warning']], 
                ['content'=>'Header Before 3', 'options'=>['colspan'=>3, 'class'=>'text-center warning']], 
            ],
            'options'=>['class'=>'skip-export'] // remove this row from export
        ]
    ],
    'toolbar' =>  [
        ['content'=>
            Html::button('&lt;i class="glyphicon glyphicon-plus">&lt;/i>', ['type'=>'button', 'title'=>Yii::t('kvgrid', 'Add Book'), 'class'=>'btn btn-success', 'onclick'=>'alert("This will launch the book creation form.\n\nDisabled for this demo!");']) . ' '.
            Html::a('&lt;i class="glyphicon glyphicon-repeat">&lt;/i>', ['grid-demo'], ['data-pjax'=>0, 'class' => 'btn btn-default', 'title'=>Yii::t('kvgrid', 'Reset Grid')])
        ],
        '{export}',
        '{toggleData}'
    ],
    'pjax' => true,
    'bordered' => true,
    'striped' => false,
    'condensed' => false,
    'responsive' => true,
    'hover' => true,
    'floatHeader' => true,
    'floatHeaderOptions' => ['scrollingTop' => $scrollingTop],
    'showPageSummary' => true,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY
    ],
]);
```

## License

**yii2-grid** is released under the BSD 3-Clause License. See the bundled `LICENSE.md` for details.