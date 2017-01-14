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
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%plugin_setting}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return [
            ['plugin, key', 'required'],
            ['plugin, key', 'length', 'max' => 45],
            ['value', 'safe'],
            ['plugin, key, value', 'safe', 'on' => 'search'],
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
            return FALSE;
        }
        return $row->value;
    }

    public static function set($plugin, $key, $value = NULL)
    {
        $row = self::get($plugin, $key);
        if ($row === FALSE) {
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
