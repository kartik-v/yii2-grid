<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2016
 * @version   3.1.3
 */

namespace kartik\grid;

use Yii;
use Closure;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\rest\Action;
use yii\web\Response;
use yii\web\BadRequestHttpException;

/**
 * EditableAction is useful for processing the update of [[EditableColumn]] attributes via form submission. A typical
 * usage of this action in your controller could look like below:
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
 *            'editbook' => [                                   // identifier for your editable column action
 *                'class' => EditableColumnAction::className(), // action class name
 *                'modelClass' => Book::className(),            // the model for the record being edited
 *                'scenario' => Model::SCENARIO_DEFAULT,        // model scenario assigned before validation & update
 *                'outputValue' => function ($model, $attribute, $key, $index) {
 *                      return (int) $model->$attribute / 100;  // return a calculated output value if desired
 *                },
 *                'outputMessage' => function($model, $attribute, $key, $index) {
 *                      return '';                              // any custom error to return after model save
 *                },
 *                'showModelErrors' => true,                    // show model validation errors after save
 *                'errorOptions' => ['header' => '']            // error summary HTML options
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
     * @var string the scenario to be assigned to the model before it is validated and updated.
     */
    public $scenario = ActiveRecord::SCENARIO_DEFAULT;

    /**
     * @var string|Closure the output value from the editable. If set as a string, will be returned as is. If set as a
     * [[Closure]], the signature of the callback would be `function ($model, $attribute, $key, $index) { }`, where:
     * - `$model`: _\yii\base\Model_, is the model data retrieved via POST.
     * - `$attribute`: _string_, the attribute name for which the editable plugin is initialized.
     * - `$key`: _mixed_, is the model primary key value.
     * - `$index`: _integer_, is the zero-based index of the data model among the model array returned by [[dataProvider]].
     */
    public $outputValue = '';

    /**
     * @var string|Closure the output error message from the editable. If set as a string, will be returned as is. If
     * set as a [[Closure]], the signature of the callback would be `function ($model, $attribute, $key, $index) { }`, where:
     * - `$model`: _\yii\base\Model_, is the model data retrieved via POST.
     * - `$attribute`: _string_, the attribute name for which the editable plugin is initialized.
     * - `$key`: _mixed_, is the model primary key value.
     * - `$index`: _integer_, is the zero-based index of the data model among the model array returned by [[dataProvider]].
     */
    public $outputMessage = '';

    /**
     * @var boolean whether to show model errors if `outputMessage` is empty or not set.
     */
    public $showModelErrors = true;

    /**
     * @var array the special error messages configuration for displaying editable submission errors other than model
     * validation errors. The following keys can be set to configure the relevant error messages:
     *
     * - `invalidEditable`: _string_, the message to be displayed when this action has not been used with the
     *    [[EditableColumn]] or no value for `$_POST[hasEditable]` is detected over post request. If not set, this will
     *    default to the i18n translated string: `'Invalid or bad editable data'`.
     * - `invalidModel`: _string_, the message to be displayed when no valid model has been found for the editable
     *    primary key submitted over post request. If not set will default to the i18n translated string:
     *   `'No valid editable model found'`.
     * - `editableException`: _string_, the message to be displayed when an invalid editable index or model form name is
     *   available over post request. If not set will default to the i18n translated string:
     *   `'Invalid editable index or model form name'`.
     * - `saveException`: _string_, the message to be displayed for any unknown server or database exception when saving
     *   the model data and when no model errors are found. If not set will default to the i18n translated string:
     *   `'Failed to update editable data due to an unknown server error'`.
     */
    public $errorMessages = [];

    /**
     * @var array the options for error summary as supported by `options` param in `yii\helpers\Html::errorSummary()`
     */
    public $errorOptions = ['header' => ''];

    /**
     * @var boolean whether to allow access to this action for POST requests only. Defaults to `true`.
     */
    public $postOnly = true;

    /**
     * @var boolean whether to allow access to this action for AJAX requests only. Defaults to `true`.
     */
    public $ajaxOnly = true;

    /**
     * @var string allows overriding the form name which is used to access posted data
     */
    public $formName;

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
     * Validates the EditableColumn post request submission
     *
     * @return array the output for the Editable action response
     * @throws BadRequestHttpException
     */
    protected function validateEditable()
    {
        $request = Yii::$app->request;
        if ($this->postOnly && !$request->isPost || $this->ajaxOnly && !$request->isAjax) {
            throw new BadRequestHttpException('This operation is not allowed!');
        }
        $this->initErrorMessages();
        $post = $request->post();
        if (!isset($post['hasEditable'])) {
            return ['output' => '', 'message' => $this->errorMessages['invalidEditable']];
        }
        /**
         * @var ActiveRecord $model
         */
        $key = ArrayHelper::getValue($post, 'editableKey');
        $model = $this->findModel($key);
        if (!$model) {
            return ['output' => '', 'message' => $this->errorMessages['invalidModel']];
        }
        if ($this->checkAccess && is_callable($this->checkAccess)) {
            call_user_func($this->checkAccess, $this->id, $model);
        }
        $model->scenario = $this->scenario;
        $index = ArrayHelper::getValue($post, 'editableIndex');
        $attribute = ArrayHelper::getValue($post, 'editableAttribute');
        $formName = isset($this->formName) ? $this->formName: $model->formName();
        if (!$formName || is_null($index) || !isset($post[$formName][$index])) {
            return ['output' => '', 'message' => $this->errorMessages['editableException']];
        }
        $postData = [$model->formName() => $post[$formName][$index]];
        if ($model->load($postData)) {
            $params = [$model, $attribute, $key, $index];
            $message = static::parseValue($this->outputMessage, $params);
            if (!$model->save()) {
                if (!$model->hasErrors()) {
                    return ['output' => '', 'message' => $this->errorMessages['saveException']];
                }
                if (empty($message) && $this->showModelErrors) {
                    $message = Html::errorSummary($model, $this->errorOptions);
                }
            }
            $value = static::parseValue($this->outputValue, $params);
            return ['output' => $value, 'message' => $message];
        }
        return ['output' => '', 'message' => ''];
    }

    /**
     * Initializes the error messages if not set.
     */
    protected function initErrorMessages()
    {
        $this->errorMessages += [
            'invalidEditable' => Yii::t('kvgrid', 'Invalid or bad editable data'),
            'invalidModel' => Yii::t('kvgrid', 'No valid editable model found'),
            'editableException' => Yii::t('kvgrid', 'Invalid editable index or model form name'),
            'saveException' => Yii::t('kvgrid', 'Failed to update editable data due to an unknown server error'),
        ];
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
