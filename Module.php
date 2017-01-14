<?php

namespace zacksleo\yii2\plugin;

use yii;
use yii\base\Module as BaseModule;

/**
 * portal module definition class
 */
class Module extends BaseModule
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'zacksleo\yii2\plugin\controllers';

    /**
     *
     * @var string source language for translation
     */
    public $sourceLanguage = 'en-US';

    /**
     * @inheritdoc
     */
    public $layout = 'main';
    /**
     * @inheritdoc
     */

    public $pluginRoot = 'application.plugin';

    public $moduleDir;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->moduleDir = dirname(__FILE__);
        Yii::setAlias('pluginModule', $this->moduleDir);
        $this->registerTranslations();
    }

    /**
     * Registers the translation files
     */
    protected function registerTranslations()
    {
        Yii::$app->i18n->translations['zacksleo/yii2/plugin/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => $this->sourceLanguage,
            'basePath' => '@zacksleo/yii2/plugin/messages',
            'fileMap' => [
                'zacksleo/yii2/plugin/lang' => 'core.php',
            ],
        ];
    }

    /**
     * Translates a message. This is just a wrapper of Yii::t
     *
     * @see Yii::t
     *
     * @param $category
     * @param $message
     * @param array $params
     * @param null $language
     * @return string
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        return Yii::t('zacksleo/yii2/plugin/' . $category, $message, $params, $language);
    }
}
