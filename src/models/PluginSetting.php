<?php

namespace zacksleo\yii2\plugin\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{plugin_setting}}".
 *
 * The followings are the available columns in table '{{plugin_setting}}':
 * @property string $plugin
 * @property string $key
 * @property string $value
 */
class PluginSetting extends ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%plugin_setting}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['plugin', 'key'], 'required'],
            [['plugin', 'key'], 'string', 'max' => 45],
            ['value', 'safe'],
        ];
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return [
            'plugin' => 'Plugin',
            'key' => 'Key',
            'value' => 'Value',
        ];
    }


    public static function clear($plugin)
    {
        self::deleteAll([
            'plugin' => $plugin
        ]);
    }

    public static function get($plugin, $key)
    {
        /* @var $row PluginSetting */
        $row = self::findOne([
            'plugin' => $plugin,
            'key' => $key
        ]);
        if (empty($row)) {
            return false;
        }
        return $row->value;
    }

    /**
     * 获取插件的所有配置参数
     * @param $plugin
     * @return array
     */
    public static function getValues($plugin)
    {
        $tmpData = self::findAll([
            'plugin' => $plugin
        ]);
        $model = array();
        foreach ($tmpData as $value) {
            $model[$value->attributes['key']] = $value->attributes['value'];
        }
        return $model;
    }

    public static function set($plugin, $key, $value = null)
    {
        $row = self::get($plugin, $key);
        if ($row === false) {
            $model = new PluginSetting();
            $model->setIsNewRecord(true);
            $model->plugin = $plugin;
            $model->key = $key;
            $model->value = $value;
            return $model->save();
        } else {
            return self::updateAll([
                'value' => $value
            ], [
                'plugin' => $plugin,
                'key' => $key
            ]);
        }
    }

}
