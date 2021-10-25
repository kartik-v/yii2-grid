<?php

namespace kartik\grid\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;

/**
 * GridEditedRowTrait implements the specific actions and methods that can be used in any of controllers to
 * enable the Krajee GridView edited row functionality. You must define a property in your controller using this
 * trait called `editedRowConfig` (which should match the GridView::editedRowConfig specification).
 *
 * For example:
 * ```
 * class BookController extends \yii\web\Controller {
 *   use \kartik\grid\controllers\GridEditedRowTrait;
 *
 *   // @var array the configuration for the row being currently edited
 *   public $editedRowConfig = [
 *     'rowIdGetParam' => 'row',
 *     'gridIdGetParam' => 'grid',
 *     'gridFiltersSessionParam' => 'kvGridFiltersCache',
 *     'highlightClass' => 'kv-row-edit-highlight',
 *   ];
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
 * }
 * ```
 * On your view file (for update or view) - you can create a Cancel button to go back to the edited row,
 * using the *back* action. For example
 * ```
 *    use yii\helpers\Html;
 *    echo Html::a('Cancel', ['back', 'id' => $model->id], ['class' => 'btn btn-primary', 'title' => 'Go back']);
 * ```
 */
trait GridEditedRowTrait
{
    /**
     * Gets the cached query parameters from session for navigating to the just edited row
     * @param  int|string  $id  the record identifier
     * @return array
     */
    protected function getQueryParamsCached($id)
    {
        $cfg = [
            'rowIdGetParam' => 'row',
            'gridIdGetParam' => 'grid',
            'gridFiltersSessionParam' => 'kvGridFiltersCache',
            'highlightClass' => 'kv-row-edit-highlight',
        ];
        if (isset($this->editedRowConfig)) {
            $cfg = array_replace($cfg, $this->editedRowConfig);
        }
        $params = Yii::$app->session->get($cfg['gridFiltersSessionParam']);
        $default = empty($id) ? [] : [$cfg['rowIdGetParam'] => $id];
        if (empty($params)) {
            return $default;
        }

        return ArrayHelper::merge($default, Json::decode($params));
    }

    /**
     * Redirects to index page with cached query params containing the highlighted edited row
     * @param  int|string  $id  the record identifier
     * @return Response
     */
    protected function redirectIndex($id)
    {
        $queryParams = $this->getQueryParamsCached($id);

        return $this->redirect(['index'] + $queryParams);
    }

    /**
     * Go back to main index page. Use this action as a link button in your other views for cancelling and going back
     * to the index page with the highlighted edited row.
     * @param  int|string  $id  the record identifier
     * @return Response
     */
    public function actionBack($id = null)
    {
        return $this->redirectIndex($id);
    }
}