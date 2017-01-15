<?php
namespace zacksleo\yii2\plugin\controllers;

use yii;
use yii\web\Controller;
use zacksleo\yii2\plugin\models\Plugin;

class PluginController extends Controller
{

    public $layout = '/';

    public function actionIndex()
    {
        if (!$_GET['id']) {
            return $this->redirect(Yii::$app->homeUrl);
        }
        $id = $_GET['id'];
        $plugin = Plugin::findOne([
            'identify' => $id,
            'enable' => true
        ]);
        if (!$plugin) {
            $this->render('miss', array('name' => $id));
            exit;
        }
        if (!isset($_GET['action'])) {
            $action = strtolower($plugin->identify);
        } else {
            $action = strtolower($_GET['action']);
        }
        $class = $this->_loadPlugin($plugin);
        $method = 'action' . $action;
        if (method_exists($class, $method)) {
            $class->$method();
            Yii:
            app()->end();
        }
        $actions = array_change_key_case($class->actions(), CASE_LOWER);
        if ($action) {
            if (isset($actions[$action])) {
                $actionClass = $this->_loadAction($actions[$action], $plugin);
                if ($actionClass) {
                    $actionClass->Owner($class);
                    $actionClass->run();
                    Yii::app()->end();
                }
            }
        }
        $this->redirect(Yii::app()->homeUrl);
    }

    private function _loadPlugin($model)
    {
        @include_once($model->path . DIRECTORY_SEPARATOR . $model->identify . 'Plugin.php');
        $class = $model->identify . 'Plugin';
        if (!class_exists($class))
            return FALSE;
        return new $class();
    }

    private function _loadAction($file, $plugin)
    {
        @include_once($plugin->path . DIRECTORY_SEPARATOR . 'actions' . DIRECTORY_SEPARATOR . $plugin->identify . $file . '.php');
        $class = $plugin->identify . $file;
        if (!class_exists($class)) {
            return FALSE;
        }
        return new $class();
    }

}

?>
