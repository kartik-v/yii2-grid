<h1 align="center">
    <a href="http://demos.krajee.com" title="Krajee Demos" target="_blank">
        <img src="http://kartik-v.github.io/bootstrap-fileinput-samples/samples/krajee-logo-b.png" alt="Krajee Logo"/>
    </a>
    <br>
    yii2-grid
    <hr>
    <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=DTP3NZQ6G2AYU"
       title="Donate via Paypal" target="_blank"><img height="60" src="https://kartik-v.github.io/bootstrap-fileinput-samples/samples/donate.png" alt="Donate"/></a>
    &nbsp; &nbsp; &nbsp;
    <a href="https://www.buymeacoffee.com/kartikv" title="Buy me a coffee" ><img src="https://cdn.buymeacoffee.com/buttons/v2/default-yellow.png" height="60" alt="kartikv" /></a>
</h1>

<div align="center">

[![Financial Contributors on Open Collective](https://opencollective.com/yii2-grid/all/badge.svg?label=financial+contributors)](https://opencollective.com/yii2-grid)
[![Stable Version](https://poser.pugx.org/kartik-v/yii2-grid/v/stable)](https://packagist.org/packages/kartik-v/yii2-grid)
[![Unstable Version](https://poser.pugx.org/kartik-v/yii2-grid/v/unstable)](https://packagist.org/packages/kartik-v/yii2-grid)
[![License](https://poser.pugx.org/kartik-v/yii2-grid/license)](https://packagist.org/packages/kartik-v/yii2-grid)

[![Total Downloads](https://poser.pugx.org/kartik-v/yii2-grid/downloads)](https://packagist.org/packages/kartik-v/yii2-grid)
[![Monthly Downloads](https://poser.pugx.org/kartik-v/yii2-grid/d/monthly)](https://packagist.org/packages/kartik-v/yii2-grid)
[![Daily Downloads](https://poser.pugx.org/kartik-v/yii2-grid/d/daily)](https://packagist.org/packages/kartik-v/yii2-grid)

</div>

Yii2 GridView on steroids. A module with various modifications and enhancements to one of the most used widgets by Yii developers. The widget contains new additional Grid Columns with enhanced settings for Yii Framework 2.0. The widget also incorporates various Bootstrap 3.x styling options.
Refer [detailed documentation](http://demos.krajee.com/grid) and/or a [complete demo](http://demos.krajee.com/grid-demo). You can also view the [grid grouping demo here](http://demos.krajee.com/group-grid).

![GridView Screenshot](https://lh4.googleusercontent.com/-4x-CdyyZAsY/VNxLPmaaAXI/AAAAAAAAAQ8/XYYxTiQZvJk/w868-h516-no/krajee-yii2-grid.jpg)

### Docs & Demo

You can see detailed [documentation](http://demos.krajee.com/grid), [demonstration](http://demos.krajee.com/grid-demo)
and API [code documentation](https://docs.krajee.com/kartik-grid-gridview) on usage of the extension. You can also view the [grid grouping demo here](http://demos.krajee.com/group-grid).

## Installation

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).
Read this [web tip /wiki](http://webtips.krajee.com/setting-composer-minimum-stability-application/) on setting the `minimum-stability` settings for your application's composer.json.

### Pre-requisites

Install the necessary pre-requisite (Krajee Dropdown Extension) based on your bootstrap version:

- For Bootstrap v5.x install the extension `kartik-v/yii2-bootstrap5-dropdown`
- For Bootstrap v4.x install the extension `kartik-v/yii2-bootstrap4-dropdown`
- For Bootstrap v3.x install the extension `kartik-v/yii2-dropdown-x`

For example if you are using the Bootstrap v5.x add the following to the `require` section of your `composer.json` file:

```
"kartik-v/yii2-bootstrap5-dropdown": "@dev"
```

### Install

Either run:

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
    'floatHeaderOptions' => ['top' => $scrollingTop],
    'showPageSummary' => true,
    'panel' => [
        'type' => GridView::TYPE_PRIMARY
    ],
]);
```

## Contributors

### Code Contributors

This project exists thanks to all the people who contribute. [[Contribute](.github/CONTRIBUTING.md)]

<a href="https://github.com/kartik-v/yii2-grid/graphs/contributors"><img src="https://opencollective.com/yii2-grid/contributors.svg?width=890&button=false" /></a>

### Financial Contributors

Become a financial contributor and help us sustain our community. [[Contribute](https://opencollective.com/yii2-grid/contribute)]

#### Individuals

<a href="https://opencollective.com/yii2-grid"><img src="https://opencollective.com/yii2-grid/individuals.svg?width=890"></a>

#### Organizations

Support this project with your organization. Your logo will show up here with a link to your website. [[Contribute](https://opencollective.com/yii2-grid/contribute)]

<a href="https://opencollective.com/yii2-grid/organization/0/website"><img src="https://opencollective.com/yii2-grid/organization/0/avatar.svg"></a>
<a href="https://opencollective.com/yii2-grid/organization/1/website"><img src="https://opencollective.com/yii2-grid/organization/1/avatar.svg"></a>
<a href="https://opencollective.com/yii2-grid/organization/2/website"><img src="https://opencollective.com/yii2-grid/organization/2/avatar.svg"></a>
<a href="https://opencollective.com/yii2-grid/organization/3/website"><img src="https://opencollective.com/yii2-grid/organization/3/avatar.svg"></a>
<a href="https://opencollective.com/yii2-grid/organization/4/website"><img src="https://opencollective.com/yii2-grid/organization/4/avatar.svg"></a>
<a href="https://opencollective.com/yii2-grid/organization/5/website"><img src="https://opencollective.com/yii2-grid/organization/5/avatar.svg"></a>
<a href="https://opencollective.com/yii2-grid/organization/6/website"><img src="https://opencollective.com/yii2-grid/organization/6/avatar.svg"></a>
<a href="https://opencollective.com/yii2-grid/organization/7/website"><img src="https://opencollective.com/yii2-grid/organization/7/avatar.svg"></a>
<a href="https://opencollective.com/yii2-grid/organization/8/website"><img src="https://opencollective.com/yii2-grid/organization/8/avatar.svg"></a>
<a href="https://opencollective.com/yii2-grid/organization/9/website"><img src="https://opencollective.com/yii2-grid/organization/9/avatar.svg"></a>

## License

**yii2-grid** is released under the BSD-3-Clause License. See the bundled `LICENSE.md` for details.