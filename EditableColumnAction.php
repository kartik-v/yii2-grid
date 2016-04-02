<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2016
 * @version   3.1.1
 */

namespace kartik\grid;

use Yii;
use Closure;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\rest\Action;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Action for processing the editable column form submission. A typical usage of this action in your controller could
 * look like below:
 *
 * ```php
 *
 * // you can set the EditableColumn::editableOptions['formOptions']['action'] to point to the action below
 * // i.e. `/site/editbook` for the example below
 *
 * use kartik\grid\EditableColumnAction;
 * use yii\web\Controller;
 * use yii\helpers\ArrayHelper;
 * use app\models\Book;
 *
 * class SiteController extends Controller
 * {
 *    public function actions()
 *    {
 *        return array_replace_recursive(parent::actions(), [
 *            'editbook' => [                                       // identifier for your editable column action
 *                'class' => EditableColumnAction::className(),     // action class name
 *                'modelClass' => Book::className(),                // the model for the record being edited
 *                'outputValue' => function ($model, $attribute, $key, $index) {
 *                      return (int) $model->$attribute / 100;      // return a calculated output value if desired
 *                },
 *                'outputMessage' => function($model, $attribute, $key, $index) {
 *                      return '';                                  // any custom error to return after model save
 *                },
 *                'showModelErrors' => true,                        // show model validation errors after save
 *                'errorOptions' => ['header' => '']                // error summary HTML options
 *                // 'postOnly' => true,
 *                // 'ajaxOnly' => true,
 *                // 'findModel' => function($id, $action) {},
 *                // 'checkAccess' => function($action, $model) {}
 *            ]
 *        ]);
 *    }
 * }
 * ```
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class EditableColumnAction extends Action
{
    /**
     * @var string|Closure the output value from the editable. If set as a string, will be returned as is. If set as a
     * Closure, the signature of the callback would be `function ($model, $attribute, $key, $index) { }`, where:
     * - `$model`: \yii\base\Model, is the model data retrieved via POST.
     * - `$attribute`: string, the attribute name for which the editable plugin is initialized
     * - `$key`: mixed, is the model primary key value
     * - `$index`: int, is the row index for the EditableColumn cell
     */
    public $outputValue = '';

    /**
     * @var string|Closure the output error message from the editable. If set as a string, will be returned as is. If
     * set as a Closure, the signature of the callback would be `function ($model, $attribute, $key, $index) { }`, where:
     * - `$model`: \yii\base\Model, is the model data retrieved via POST.
     * - `$attribute`: string, the attribute name for which the editable plugin is initialized
     * - `$key`: mixed, is the model primary key value
     * - `$index`: int, is the row index for the EditableColumn cell
     */
    public $outputMessage = '';

    /**
     * @var bool whether to show model errors if `outputMessage` is empty or not set.
     */
    public $showModelErrors = true;

    /**
     * @var array the options for error summary as supported by `options` param in `yii\helpers\Html::errorSummary()`
     */
    public $errorOptions = ['header' => ''];

    /**
     * @var bool whether to allow access to this action for POST requests only. Defaults to `true`.
     */
    public $postOnly = true;

    /**
     * @var bool whether to allow access to this action for AJAX requests only. Defaults to `true`.
     */
    public $ajaxOnly = true;

    /**
     * @inheritdoc
     */
    public function run()
    {
        $m = Yii::$app->getModule(Module::MODULE);
        $out = $this->validateEditable();
        unset($m);
        return Yii::createObject(['class' => Response::className(), 'format' => Response::FORMAT_JSON, 'data' => $out]);
    }

    /**
     * Validates the EditableColumn post submission
     *
     * @return array the output for the Editable action response
     * @throws BadRequestHttpException
     */
    protected function validateEditable()
    {
        if ($this->checkAccess && is_callable($this->checkAccess)) {
            call_user_func($this->checkAccess, $this->id);
        }
        $request = Yii::$app->request;
        if ($this->postOnly && !$request->isPost || $this->ajaxOnly && !$request->isAjax) {
            throw new BadRequestHttpException('This operation is not allowed!');
        }
        $post = $request->post();
        if (!isset($post['hasEditable'])) {
            return ['output' => '', 'message' => Yii::t('kvgrid', 'Invalid or bad editable data')];
        }
        /**
         * @var ActiveRecord $modelClass
         * @var ActiveRecord $model
         */
        $modelClass = $this->modelClass;
        $key = ArrayHelper::getValue($post, 'editableKey');
        $model = $modelClass::findOne($key);
        if (!$model) {
            return ['output' => '', 'message' => Yii::t('kvgrid', 'No valid editable model found')];
        }
        $index = ArrayHelper::getValue($post, 'editableIndex');
        $attribute = ArrayHelper::getValue($post, 'editableAttribute');
        $formName = $model->formName();
        if (!$formName || is_null($index) || !isset($post[$formName][$index])) {
            return ['output' => '', 'message' => Yii::t('kvgrid', 'Invalid editable index or model form name')];
        }
        $postData = [$formName => $post[$formName][$index]];
        if ($model->load($postData)) {
            $params = [$model, $attribute, $key, $index];
            $value = static::parseValue($this->outputValue, $params);
            if (!$model->save()) {
                $message = static::parseValue($this->outputMessage, $params);
                if (empty($message) && $this->showModelErrors) {
                    $message = Html::errorSummary($model, $this->errorOptions);
                }
            } else {
                $message = static::parseValue($this->outputMessage, $params);
            }
            return ['output' => $value, 'message' => $message];
        }
        return ['output' => '', 'message' => ''];
    }

    /**
     * Parses a variable if callable and computes and returns value accordingly
     *
     * @param mixed $var the variable to be parsed
     * @param array $params the function parameters if $var is callable
     *
     * @return mixed
     */
    protected static function parseValue($var, $params = [])
    {
        return is_callable($var) ? call_user_func_array($var, $params) : $var;
    }
}
