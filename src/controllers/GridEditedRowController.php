<?php

namespace kartik\grid\controllers;

use yii\web\Controller;
/**
 * GridEditedRowController implements the specific actions and methods that can be used in any of controllers for
 * handling standard CRUD operations to enable the Krajee GridView edited row functionality. For example:
 * ```
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
 * On your view file (for update or view) - you can create a Cancel button to go back to the edited row,
 * using the *back* action. For example
 * ```
 *    use yii\helpers\Html;
 *    echo Html::a('Cancel', ['back', 'id' => $model->id], ['class' => 'btn btn-primary', 'title' => 'Go back']);
 * ```
 */
class GridEditedRowController extends Controller
{
    use GridEditedRowTrait;

    /**
     * @var array the configuration for the row being currently edited. This matches the GridView::editedRowConfig
     * property and must exactly match the settings you have set in your GridView widget on the index page.
     */
    public $editedRowConfig = [
        'rowIdGetParam' => 'row',
        'gridIdGetParam' => 'grid',
        'gridFiltersSessionParam' => 'kvGridFiltersCache',
        'highlightClass' => 'kv-row-edit-highlight',
    ];
}