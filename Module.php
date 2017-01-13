<?php

namespace zacksleo\yii2\wechat;

use yii;
use yii\helpers\Url;

/**
 * portal module definition class
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'zacksleo\yii2\wechat\controllers';

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
    //public $layoutPath = 'app\modules\wechat\themes\wechat\views\layouts';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    /**
     * Registers the translation files
     */
    protected function registerTranslations()
    {
        Yii::$app->i18n->translations['zacksleo/yii2/wechat/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => $this->sourceLanguage,
            'basePath' => '@zacksleo/yii2/wechat/messages',
            'fileMap' => [
                'zacksleo/yii2/wechat/core' => 'core.php',
                'zacksleo/yii2/wechat/tree' => 'tree.php',
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
        return Yii::t('zacksleo/yii2/wechat/' . $category, $message, $params, $language);
    }
}
