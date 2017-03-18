<?php

namespace zacksleo\yii2\plugin\components;

use yii\web\NotFoundHttpException;
use zacksleo\yii2\plugin\models\Plugin;

/**
 * Class PluginManger
 * @package zacksleo\yii2\plugin\components
 */
class PluginManger
{
    protected $pluginCache;

    const STATUS_NOT_INSTALLED = 0;
    const STATUS_INSTALLED = 1;
    const STATUS_ENABLED = 2;

    /**
     * @param $plugin \zacksleo\yii2\plugin\components\Plugin;
     * @return bool
     */
    public function install($plugin)
    {
        if (!$this->assertPlugin($plugin)) {
            return false;
        }
        if (!$plugin->install()) {
            return false;
        }
        if (!$this->registerPlugin($plugin)) {
            return false;
        }
        return true;
    }

    /**
     * @param $plugin \zacksleo\yii2\plugin\components\Plugin
     * @return bool
     */
    public function uninstall($plugin)
    {
        if (!$plugin->uninstall() && !$plugin->clear()) {
            return false;
        }
        if (!$this->unregisterPlugin($plugin)) {
            return false;
        }
        return true;
    }

    /**
     * @param $plugin \zacksleo\yii2\plugin\components\Plugin
     * @return bool|false|int
     */
    public function enable($plugin)
    {
        $plugin = $this->findModel($plugin->identify);
        if (!$plugin) {
            return false;
        }
        $plugin->enable = 1;
        return $plugin->update();
    }

    /**
     * @param $plugin \zacksleo\yii2\plugin\components\Plugin
     * @return bool|false|int
     */
    public function disable($plugin)
    {
        $plugin = $this->findModel($plugin->identify);
        if (!$plugin) {
            return false;
        }
        $plugin->enable = 0;
        return $plugin->update();
    }

    /**
     * @param $plugin \zacksleo\yii2\plugin\components\Plugin;
     * @return int
     */
    public function status($plugin)
    {
        $plugin = $this->findModel($plugin->identify);
        if (!$plugin) {
            return self::STATUS_NOT_INSTALLED;
        } elseif ($plugin->enable) {
            return self::STATUS_ENABLED;
        } else {
            return self::STATUS_INSTALLED;
        }
    }

    /**
     * @param $plugin \zacksleo\yii2\plugin\components\Plugin
     * @return bool
     */
    protected function registerPlugin($plugin)
    {
        $model = new Plugin();
        $model->identify = $plugin->identify;
        $model->path = $plugin->pluginDir;
        $model->hooks = serialize($plugin->hooks());
        return $model->save();
    }

    /**
     * @param $plugin \zacksleo\yii2\plugin\components\Plugin
     * @return bool
     */
    protected function unregisterPlugin($plugin)
    {
        $row = Plugin::deleteAll([
            'identify' => $plugin->identify
        ]);

        return ($row) ? true : false;
    }

    /**
     * @param $plugin \zacksleo\yii2\plugin\components\Plugin
     * @return bool
     */
    protected function assertPlugin($plugin)
    {
        $hooks = $plugin->hooks();
        $r1 = !empty($hooks);
        $r2 = $plugin->identify ? true : false;
        $r3 = !$this->status($plugin);
        $r4 = !Plugin::findOne(['identify' => $plugin->identify]);
        return $r1 && $r2 && $r3 && $r4;
    }

    /**
     * Finds the App model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $identify
     * @return Plugin|boolean the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($identify)
    {
        if (($model = Plugin::findOne(['identify' => $identify])) !== null) {
            return $model;
        } else {
            return false;
        }
    }
}

