Change Log: `yii2-grid`
=======================

**Date:** 01-Sep-2016


Revision 2:
Adding to line 112 was problematic as well, instead I modified the code at line 239 as follows;
```
            if (isExpanded($icon)) {
                if(vInd !== undefined) {
                    if (detailUrl) {
                        loadDetail(function () {
                            expandRow(false);
                        });
                    } else {
                        expandRow(false);
                    }
                }
            }
```
I will repoen the pull request after more testing.

Revision 1:
Adding the code to line 103 broke expand / collapse all functionality. Moving the code to line 112 after "var loadDetail = function (postProcess) {" fixed the problem.

Initial release

Fix for pjax gridview using expandRow with detailUrl pjax form.


Problem: expanded rows disappear and new rows get added with data-index="undefined"

Steps to reproduce:
1. Expand two rows
2. When you submit the form on the second expanded row, the first row disappears

Fix: modify the following lines in /assets/js/kv-grid-expand.js at line 103

```
                    if(vInd === undefined) {
                        return;
                    }
```

CODES:

View index.php:
```
<?php
$columns = [
    [
        'class' => '\kartik\grid\ExpandRowColumn',
        'value' => function ($model, $key, $index) {
            return GridView::ROW_COLLAPSED;
        },
        'detailUrl' => Url::to('/cart-order/form'),
    ],
];

echo GridView::widget([
        'id' => 'orders-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
        'pjax' => true,
        'pjaxSettings' => [
            'options' => [
                'formSelector' => '.gridview-filter-form'
            ],
        ],
        'bordered' => true,
        'striped' => false,
        'condensed' => true ,
        'responsive' => true,
        'responsiveWrap' => false,
        'hover' => true,
    ]);
?>
```

View _form.php:
```
    <?php yii\widgets\Pjax::begin([
        'id' => 'w-' . $model->id,
        'enablePushState' => false,
        'formSelector' => '#f-' . $model->id,
        'clientOptions' => [
            'container' => '#w-' . $model->id,
            'skipOuterContainers' => true,
        ]
    ]) ?>
    <?php $form = ActiveForm::begin([
        'id' => 'f-' . $model->id,
        'options' => ['data-pjax' => true]
    ]); ?>
    <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>
    <?= rand(0, 212121); ?><br>
    <?= $model->id; ?>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Send Confirmation SMS'), ['class' => 'btn btn-success btn-block', 'name' => 'submit']) ?>
    </div>
    <?php ActiveForm::end(); ?>
    <?php Pjax::end(); ?>

```

Controller CartOrderController.php:

```
    public function actionIndex()
    {
        $searchModel = new CartOrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionForm($id = null)
    {
        if (isset($_POST['expandRowKey'])) {
            $id =  $_POST['expandRowKey'];
        }elseif (isset($_POST['CartOrder']['id'])) {
            $id = $_POST['CartOrder']['id'];
        }
        if (is_numeric($id)) {
            return $this->renderAjax('_form', [
                'model' => $this->findModel($id),
            ]);
        } else {
            return '<div class="alert alert-danger">No data found</div>';
        }

    }

```
