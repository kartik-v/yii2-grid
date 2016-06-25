<?php

/**
 * @package   yii2-grid
 * @author    Kartik Visweswaran <kartikv2@gmail.com>
 * @copyright Copyright &copy; Kartik Visweswaran, Krajee.com, 2014 - 2016
 * @version   3.1.2
 */

namespace kartik\grid\controllers;

use Yii;
use yii\helpers\HtmlPurifier;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Response;
use kartik\grid\GridView;
use kartik\mpdf\Pdf;

class ExportController extends Controller
{
    /**
     * Download the exported file
     *
     * @return mixed
     */
    public function actionDownload()
    {
        $request = Yii::$app->request;
        $type = $request->post('export_filetype', 'html');
        $name = $request->post('export_filename', Yii::t('kvgrid', 'export'));
        $content = $request->post('export_content', Yii::t('kvgrid', 'No data found'));
        $mime = $request->post('export_mime', 'text/plain');
        $encoding = $request->post('export_encoding', 'utf-8');
        $bom = $request->post('export_bom', true);
        $config = $request->post('export_config', '{}');
        if ($type == GridView::PDF) {
            $config = Json::decode($config);
            $this->generatePDF($content, "{$name}.pdf", $config);
            /** @noinspection PhpInconsistentReturnPointsInspection */
            return;
        }  elseif ($type == GridView::HTML) {
            $content = HtmlPurifier::process($content);
        } elseif (($type == GridView::CSV || $type == GridView::TEXT) && $encoding == 'utf-8' && $bom) {
            $content = chr(239) . chr(187) . chr(191) . $content; // add BOM
        }
        $this->setHttpHeaders($type, $name, $mime, $encoding);
        return $content;
    }

    /**
     * Generates the PDF file
     *
     * @param string $content the file content
     * @param string $filename the file name
     * @param array  $config the configuration for yii2-mpdf component
     *
     * @return void
     */
    protected function generatePDF($content, $filename, $config = [])
    {
        unset($config['contentBefore'], $config['contentAfter']);
        $config['filename'] = $filename;
        $config['methods']['SetAuthor'] = ['Krajee Solutions'];
        $config['methods']['SetCreator'] = ['Krajee Yii2 Grid Export Extension'];
        $config['content'] = $content;
        $pdf = new Pdf($config);
        echo $pdf->render();
    }

    /**
     * Sets the HTTP headers needed by file download action.
     *
     * @param string $type the file type
     * @param string $name the file name
     * @param string $mime the mime time for the file
     * @param string $encoding the encoding for the file content
     *
     * @return void
     */
    protected function setHttpHeaders($type, $name, $mime, $encoding = 'utf-8')
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        if (strstr($_SERVER["HTTP_USER_AGENT"], "MSIE") == false) {
            header("Cache-Control: no-cache");
            header("Pragma: no-cache");
        } else {
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Pragma: public");
        }
        header("Expires: Sat, 26 Jul 1979 05:00:00 GMT");
        header("Content-Encoding: {$encoding}");
        header("Content-Type: {$mime}; charset={$encoding}");
        header("Content-Disposition: attachment; filename={$name}.{$type}");
        header("Cache-Control: max-age=0");
    }
}
