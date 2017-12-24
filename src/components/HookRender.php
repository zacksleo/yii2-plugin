<?php

namespace zacksleo\yii2\plugin\components;

use yii;
use zacksleo\yii2\plugin\models\Plugin;

$moduleDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..';
Yii::setAlias('pluginModule', $moduleDir);

class HookRender extends yii\base\Object
{
    protected $hooks = [];

    public function init()
    {
        $this->getHooks();
    }

    public function render($pos)
    {
        if (empty($this->hooks)) {
            return;
        }
        if (!isset($this->hooks[$pos])) {
            return;
        }
        $hooks = $this->hooks[$pos];
        if (!$hooks) {
            return;
        }
        foreach ($hooks as $hook) {
            /* @var $plugin \zacksleo\yii2\plugin\components\Plugin */
            $class = $hook['path'];
            if (!class_exists($class)) {
                continue;
            }
            $plugin = new $class();
            $act = explode('.', $hook['hook']);
            if ($act[1]) {
                $h = $plugin->loadHook($hook['hook']);
                if (!$h) {
                    continue;
                }
                return $h->run();
            } else {
                $render = $act[0];
                return $plugin->$render();
            }
        }
    }

    protected function getHooks()
    {
        $plugins = Plugin::findAll(['enable' => true]);
        if (!$plugins) {
            return;
        }
        foreach ($plugins as $plugin) {
            foreach (unserialize($plugin->hooks) as $pos => $hook) {
                $this->hooks[$pos][] = ['identify' => $plugin->identify, 'path' => $plugin->path, 'hook' => $hook];
            }
        }
        return true;
    }
}
