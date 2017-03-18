<?php
namespace zacksleo\yii2\plugin\components;

use yii;
use zacksleo\yii2\plugin\models\Plugin;
use zacksleo\yii2\plugin\components;

$moduleDir = dirname(__FILE__) . DIRECTORY_SEPARATOR . '..';
Yii::setAlias('pluginModule', $moduleDir);

class HookRender
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
        $hooks = $this->hooks[$pos];
        if (!$hooks) {
            return;
        }
        foreach ($hooks as $hook) {
            /* @var $plugin \zacksleo\yii2\plugin\components\Plugin */
            $class = new \ReflectionClass($hook['path'] . '\\' . $hook['identify'] . 'Plugin');
            $instance = $class->newInstance();
            if (!class_exists($instance)) {
                continue;
            }
            $plugin = new $class();
            $act = explode('.', $hook['hook']);
            if ($act[1]) {
                $h = $plugin->loadHook($hook['hook']);
                if (!$h) {
                    continue;
                }
                $h->run();
            } else {
                $render = $act[0];
                $plugin->$render();
            }
        }
    }

    protected function getHooks()
    {
        $plugins = Plugin::findOne(['enable' => true]);
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

