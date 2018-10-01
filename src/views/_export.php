<?php
/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2018
 * @version   3.2.6
 */

use yii\helpers\Html;

/**
 * Grid export data handling form
 *
 * @var string $action the form action
 * @var string $module the grid module identifier
 * @var array $formOptions the export form HTML attributes
 * @var string $encoding the export encoding
 * @var bool $bom the export bom setting
 */

echo Html::beginForm($action, 'post', $formOptions);
echo Html::hiddenInput('module_id', $module);
echo Html::hiddenInput('export_hash');
echo Html::hiddenInput('export_filetype');
echo Html::hiddenInput('export_filename');
echo Html::hiddenInput('export_mime');
echo Html::hiddenInput('export_config');
echo Html::hiddenInput('export_encoding', $encoding);
echo Html::hiddenInput('export_bom', $bom);
echo Html::textarea('export_content');
echo Html::endForm();