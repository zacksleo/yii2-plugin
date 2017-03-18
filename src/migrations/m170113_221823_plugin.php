<?php

/**
 * Yii-Plugin module.
 *
 * @author zacksleo <zacksleo@gmail.com>
 * @link https://github.com/zacksleo/yii2-plugin
 * @license MIT
 * @version 1.0
 */

use yii\db\Migration;

class m170113_221823_plugin extends Migration
{

    public function up()
    {
        $this->createTable('{{%plugin}}', [
            'plugin_id' => $this->primaryKey(),
            'identify' => $this->string(45)->notNull()->unique(),
            'path' => $this->string()->notNull(),
            'hooks' => $this->text()->notNull(),
            'enable' => $this->boolean()->defaultValue(0),
        ], 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB');

        $this->createTable('{{%plugin_setting}}', [
            'plugin' => $this->string(45)->notNull(),
            'key' => $this->string(45)->notNull(),
            'value' => $this->text(),
        ], 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB');

        $this->addPrimaryKey('plugin_key', '{{%plugin_setting}}', [
            'plugin',
            'key'
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%plugin}}');
        $this->dropTable('{{%plugin_setting}}');
    }

}