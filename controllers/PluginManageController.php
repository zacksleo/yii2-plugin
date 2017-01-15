<?php
namespace zacksleo\yii2\plugin\controllers;

use yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use zacksleo\yii2\plugin\components\PluginManger;

/**
 * Class PluginManageController
 * @package zacksleo\yii2\plugin\controllers
 * @property \zacksleo\yii2\plugin\components\PluginManger $pluginManger
 */
class PluginManageController extends Controller
{

    public $layout = '/layout/sidebar';
    public $adminLayout;
    public $menu = array();
    public $defaultIcon;
    public $module;
    private $folder;
    private $plugins = array();
    private $pluginManger;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'upgrade'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],

        ];
    }

    public function init()
    {
        parent::init();
        $this->module = Yii::$app->getModule('plugin');
        $this->folder = Yii::getAlias($this->module->pluginRoot);
        $this->pluginManger = new PluginManger();
        $this->adminLayout = $this->module->layout;
        $this->defaultIcon = Yii::$app->getAssetManager()->publish($this->module->moduleDir . DIRECTORY_SEPARATOR . 'default.png');
    }

    public function actionIndex()
    {
        $this->_getPlugins($this->folder);
        $this->_loadPlugins();
        $this->_setMenu();
        $plugins = array(PluginManger::STATUS_ENABLED => Array(), PluginManger::STATUS_INSTALLED => array(), PluginManger::STATUS_NOT_INSTALLED => array());

        foreach ($this->plugins as $plugin) {
            switch ($this->pluginManger->status($plugin['plugin'])) {
                case PluginManger::STATUS_ENABLED:
                    $plugins[PluginManger::STATUS_ENABLED][] = $plugin;
                    break;
                case PluginManger::STATUS_INSTALLED:
                    $plugins[PluginManger::STATUS_INSTALLED][] = $plugin;
                    break;
                case PluginManger::STATUS_NOT_INSTALLED:
                    $plugins[PluginManger::STATUS_NOT_INSTALLED][] = $plugin;
                    break;
            }
        }
        return $this->render('index', ['plugins' => $plugins]);
    }

    public function actionSetting($id)
    {
        $this->_setMenu();
        $plugin = $this->_loadPluginFromIdentify($id);
        if (method_exists($plugin, 'AdminCp')) {
            ob_start();
            $plugin->AdminCp();
            $content = ob_get_contents();
            ob_end_clean();
        } elseif ($plugin->setAdminCp()) {
            $file = $plugin->setAdminCp();
            include_once($plugin->pluginDir . DIRECTORY_SEPARATOR . $file . '.php');
            if (strpos($file, '/') !== FALSE) {
                $class = end(explode('/', $file));
            } else {
                $class = $file;
            }
            if (!class_exists($class)) {
                $this->redirect(array('plugin/PluginManage/index'));
                exit;
            }
            $AdminCp = new $class();
            $AdminCp->Owner($plugin);
            ob_start();
            $AdminCp->run();
            $content = ob_get_contents();
            ob_end_clean();
        } else {
            return $this->redirect(array('plugin/PluginManage/index'));
        }
        return $this->render('setting', ['name' => $plugin->name, 'content' => $content]);
    }

    public function actionMarket()
    {

    }

    public function actionInstall()
    {
        if (!isset($_POST['id']))
            $this->_ajax(0);
        $id = $_POST['id'];
        $plugin = $this->_loadPluginFromIdentify($id);
        $result = $this->pluginManger->install($plugin);
        if ($result) {
            $this->_ajax(1);
        } else {
            $this->_ajax(0);
        }
    }

    public function actionUninstall()
    {
        if (!isset($_POST['id']))
            $this->_ajax(0);
        $id = $_POST['id'];
        $plugin = $this->_loadPluginFromIdentify($id);
        $result = $this->pluginManger->uninstall($plugin);
        if ($result) {
            $this->_ajax(1);
        } else {
            $this->_ajax(0);
        }
    }

    public function actionEnable()
    {
        if (!isset($_POST['id']))
            $this->_ajax(0);
        $id = $_POST['id'];
        $plugin = $this->_loadPluginFromIdentify($id);
        if ($this->pluginManger->enable($plugin)) {
            $this->_setMenu(true);
            $this->_ajax(1);
        } else {
            $this->_ajax(0);
        }
    }

    public function actionDisable()
    {
        if (!isset($_POST['id']))
            $this->_ajax(0);
        $id = $_POST['id'];
        $plugin = $this->_loadPluginFromIdentify($id);
        if ($this->pluginManger->disable($plugin)) {
            $this->_setMenu(TRUE);
            $this->_ajax(1);
        } else {
            $this->_ajax(0);
        }
    }

    private function _getPlugins($folder)
    {
        if ($handle = opendir($folder)) {
            while (FALSE !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if (is_dir($folder . DIRECTORY_SEPARATOR . $file)) {
                        $this->_getPlugins($folder . DIRECTORY_SEPARATOR . $file);
                    } else {
                        if (preg_match('/^[\w_]+Plugin.php$/', $file))
                            $this->plugins[] = array('path' => $folder, 'pluginEntry' => $file);
                    }
                }
            }
            closedir($handle);
        } else {
            return FALSE;
        }
    }

    private function _loadPlugins()
    {
        $plugins = array();
        foreach ($this->plugins as $k => $plugin) {
            @include_once($plugin['path'] . DIRECTORY_SEPARATOR . $plugin['pluginEntry']);
            $class = substr($plugin['pluginEntry'], 0, strlen($plugin['pluginEntry']) - 4);
            if (class_exists($class)) {
                $this->plugins[$k]['plugin'] = new $class();
                $this->plugins[$k]['status'] = $this->pluginManger->Status($this->plugins[$k]['plugin']);
                $this->plugins[$k]['identify'] = $this->plugins[$k]['plugin']->identify;
                $plugins[$k] = $this->plugins[$k];
                unset($plugins[$k]['plugin']);
            }
        }
        Yii::$app->cache->set('PluginList', $plugins);
        return true;
    }

    private function _loadPluginFromIdentify($identify)
    {
        $plugins = Yii::$app->cache->get('PluginList');
        if (!$plugins) {
            $this->_getPlugins($this->folder);
            $this->_loadPlugins();
            $plugins = $this->plugins;
        }
        foreach ($plugins as $plugin) {
            if ($plugin['identify'] == $identify) {
                @include_once($plugin['path'] . DIRECTORY_SEPARATOR . $plugin['pluginEntry']);
                $class = substr($plugin['pluginEntry'], 0, strlen($plugin['pluginEntry']) - 4);
                if (class_exists($class)) {
                    return new $class();
                }
            }
        }
        return FALSE;
    }

    private function _setMenu($force = FALSE)
    {
        if (!$force)
            $cache = Yii::$app->cache->get('PluginMenu');
        if ($cache) {
            $this->menu = $cache;
            return;
        }
        $this->menu = array();
        if (empty($this->plugins)) {
            $this->_getPlugins($this->folder);
            $this->_loadPlugins();
        }
        foreach ($this->plugins as $plugin) {
            if ($this->pluginManger->Status($plugin['plugin']) != PluginManger::STATUS_ENABLED) {
                continue;
            }
            if (!method_exists($plugin, 'AdminCp') && !$plugin['plugin']->setAdminCp()) {
                continue;
            }
            $this->menu[] = array('label' => $plugin['plugin']->name, 'url' => array('/plugin/PluginManage/setting', 'id' => $plugin['plugin']->identify));
        }
        Yii::$app->cache->set('PluginMenu', $this->menu);
    }

    private function _ajax($status, $data = NULL)
    {
        header('Content-type: application/json');
        echo json_encode(array('status' => $status, 'data' => $data));
        Yii::$app->end();
    }

}

?>
