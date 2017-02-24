<?php

namespace zacksleo\yii2\plugin\components;

use yii;

/**
 * 插件管理页类
 * @package zacksleo\yii2\plugin\components
 */
class PluginAdmin extends PluginAbstract
{

    /**
     * Stores a flash message. A flash message is available only in the current and the next requests
     * @param string $key key identifying the flash message
     * @param mixed $value flash message
     */
    public function setFlash($key, $value)
    {
        Yii::$app->session->setFlash($key, $value);
    }

    /**
     * Returns a flash message. A flash message is available only in the current and the next requests.
     * @param  string $key key identifying the flash message
     * @param  mixed $defaultValue value to be returned if the flash message is not available.
     * @param  boolean $delete whether to delete this flash message after accessing it. Defaults to true.
     * @return mixed                the message message
     */
    public function getFlash($key, $defaultValue = NULL, $delete = true)
    {
        return Yii::$app->session->getFlash($key, $defaultValue, $delete);
    }

    /**
     * Returns all flash messages. This method is similar to getFlash except that it returns all currently available flash messages.
     * @param  boolean $delete whether to delete the flash messages after calling this method.
     * @return array          flash messages (key => message).
     */
    public function getFlashes($delete = true)
    {
        return Yii::$app->session->getAllFlashes();
    }

}