<?php

namespace zacksleo\yii2\plugin\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{plugins}}".
 *
 * The followings are the available columns in table '{{plugins}}':
 * @property integer $plugin_id
 * @property string $identify
 * @property string $path
 * @property string $hooks
 * @property integer $enable
 */
class Plugin extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%plugin}}';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {

        return [
            ['identify, path, hooks', 'required'],
            ['enable', 'numerical', 'integerOnly' => true],
            ['identify', 'length', 'max' => 45],
            ['path', 'length', 'max' => 255],
            ['identify', 'unique'],
            ['plugin_id, identify, path, hooks, enable', 'safe', 'on' => 'search'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'plugin_id' => 'Plugin',
            'identify' => 'Identify',
            'path' => 'Path',
            'hooks' => 'Hooks',
            'enable' => 'Enable',
        ];
    }

}
