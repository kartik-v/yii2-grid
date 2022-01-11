<?php

namespace kartik\grid\controllers;

use yii\web\Controller;

/**
 * GridEditedRowController implements the actions for controlling the record edits via the GridView and displaying the
 * last edited row in the [[kartik\grid\GridView]].
 *
 * The standard CRUD operations have been enhanced to enable the Krajee GridView edited row functionality. When an user
 * returns back to the GridView `index` page from a `create`, `update` or `view` page, the following functionalities are
 * automatically enabled:
 *
 * - page automatically scrolls and user is directly led to the specific row last edited
 * - the last edited row is also specially highlighted
 *
 * To use this in your application, extend your Controller class from [[GridEditedRowController]] or use
 * the [[GridEditedRowTrait]] in your controller class.
 *
 * For example,
 *
 * ```php
 * class BookController extends \kartik\grid\controllers\GridEditedRowController {
 *
 *   // your model UPDATE action
 *   public function actionUpdate($id) {
 *     $model = $this->findModel($id);
 *     if ($model->load(Yii::$app->request->post()) && $model->save()) {
 *       Yii::$app->session->setFlash('success', "Updated book # {$id} successfully.");
 *       return $this->redirectIndex($id); // USE this for redirecting to edited row
 *     } else {
 *       return $this->render('update', ['model' => $model, ]);
 *     }
 *   }
 *
 * }
 * ```
 *
 * On your view file (for update or view) - you can create a Cancel button to go back to the edited row,
 * using the *back* action. For example,
 *
 * ```php
 *    use yii\helpers\Html;
 *    echo Html::a('Cancel', ['back', 'id' => $model->id], ['class' => 'btn btn-primary', 'title' => 'Go back']);
 * ```
 */
class GridEditedRowController extends Controller
{
    use GridEditedRowTrait;

    /**
     * @var array the configuration for the row being currently edited. This matches the [[kartik\grid\GridView::editedRowConfig]]
     * property and must exactly match the settings you have set in your GridView widget on the index page.
     */
    public $editedRowConfig = [
        'rowIdGetParam' => 'row',
        'gridIdGetParam' => 'grid',
        'gridFiltersSessionParam' => 'kvGridFiltersCache',
        'highlightClass' => 'kv-row-edit-highlight',
    ];
}