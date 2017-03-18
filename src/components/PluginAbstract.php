<?php

namespace zacksleo\yii2\plugin\components;

abstract class PluginAbstract extends PluginBase
{
    public $plugin;

    public function run()
    {
        return false;
    }

    public function __get($name)
    {
        $value = parent::__get($name);
        if (!$value && $this->plugin->$name !== null) {
            return $this->plugin->$name;
        } else {
            return $value;
        }
    }

    public function Owner($plugin)
    {
        $this->plugin = $plugin;
        $this->i18n = $plugin->i18n;
        $this->pluginDir = $plugin->pluginDir;
    }
}

