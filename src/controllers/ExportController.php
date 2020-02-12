<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2020
 * @version   3.3.5
 */

namespace kartik\grid\controllers;

use Yii;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;
use kartik\base\Config;
use kartik\grid\GridView;
use kartik\mpdf\Pdf;
use kartik\grid\Module;

/**
 * ExportController manages actions for downloading the [[GridView]] tabular content in various export formats.
 *
 * @author Kartik Visweswaran <kartikv2@gmail.com>
 * @since 1.0
 */
class ExportController extends Controller
{
    /**
     * Download the exported file
     *
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public function actionDownload()
    {
        /**
         * @var Module $module
         */
        $request = Yii::$app->request;
        $moduleId = $request->post('module_id', Module::MODULE);
        $module = Config::getModule($moduleId, Module::class);
        $type = $request->post('export_filetype', 'html');
        $name = $request->post('export_filename', Yii::t('kvgrid', 'export'));
        $content = $request->post('export_content', Yii::t('kvgrid', 'No data found'));
        $mime = $request->post('export_mime', 'text/plain');
        $encoding = $request->post('export_encoding', 'utf-8');
        $bom = $request->post('export_bom', 1);
        $hashConfig = $request->post('hash_export_config', 0);
        $hashConfig = empty($hashConfig) ? 0 : 1;
        $config = $request->post('export_config', '{}');
        $cfg = empty($hashConfig) ? '' : $config;
        $oldHash = $request->post('export_hash');
        $newData = $moduleId . $name . $mime . $encoding . $bom . $hashConfig . $cfg;
        $security = Yii::$app->security;
        $salt = $module->exportEncryptSalt;
        $newHash = $security->hashData($newData, $salt);
        if (!$security->validateData($oldHash, $salt) || $oldHash !== $newHash) {
            $params = "\nOld Hash:{$oldHash}\nNew Hash:{$newHash}\n";
            throw new InvalidCallException("The parameters for yii2-grid export seem to be tampered. Please retry!{$params}");
        }
        if ($type == GridView::PDF) {
            $config = Json::decode($config);
            return $this->generatePDF($content, "{$name}.pdf", $config);
        } elseif ($type == GridView::CSV || $type == GridView::TEXT) {
            if ($encoding != 'utf-8') {
                $content = mb_convert_encoding($content, $encoding, 'utf-8');
            } elseif ($bom) {
                $content = chr(239) . chr(187) . chr(191) . $content; // add BOM
            }
        }
        $this->setHttpHeaders($type, $name, $mime, $encoding);
        return $content;
    }

    /**
     * Generates the PDF file
     *
     * @param string $content the file content
     * @param string $filename the file name
     * @param array $config the configuration for yii2-mpdf component
     *
     * @return Response
     * @throws InvalidConfigException
     */
    protected function generatePDF($content, $filename, $config = [])
    {
        unset($config['contentBefore'], $config['contentAfter']);
        $config['filename'] = $filename;
        $config['methods']['SetAuthor'] = [Yii::t('kvgrid', 'Krajee Solutions')];
        $config['methods']['SetCreator'] = [Yii::t('kvgrid', 'Krajee Yii2 Grid Export Extension')];
        $config['content'] = $content;
        $pdf = new Pdf($config);
        return $pdf->render();
    }

    /**
     * Sets the HTTP headers needed by file download action.
     *
     * @param string $type the file type
     * @param string $name the file name
     * @param string $mime the mime time for the file
     * @param string $encoding the encoding for the file content
     */
    protected function setHttpHeaders($type, $name, $mime, $encoding = 'utf-8')
    {
        $response = Yii::$app->response;
        $response->format = Response::FORMAT_RAW;
        $headers = $response->getHeaders();
        $headers->set('Content-Type', "{$mime}; charset={$encoding}");
        $headers->set('Content-Transfer-Encoding', $encoding);
        $headers->set('Cache-Control', 'public, must-revalidate, max-age=0');
        $headers->set('Pragma', 'public');
        $headers->set('Expires', 'Sat, 26 Jul 1997 05:00:00 GMT');
        $headers->set('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT');
        $headers->set('Content-Disposition', "attachment; filename={$name}.{$type}");
    }
}
