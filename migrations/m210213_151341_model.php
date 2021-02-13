<?php

use yii\db\Migration;

/**
 * Class m210213_151341_model
 */
class m210213_151341_model extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->getDriverName() === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_bin ENGINE=InnoDB';
        }

        $this->createTable('{{%model}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'speed' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('model_speed', '{{%model}}', 'speed');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%model}}');
    }
}
