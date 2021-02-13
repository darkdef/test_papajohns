<?php

use yii\db\Migration;

/**
 * Class m210213_151340_driver
 */
class m210213_151340_driver extends Migration
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

        $this->createTable('{{%driver}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'birth_date' => $this->date()->notNull(),
        ], $tableOptions);

        $this->createIndex('driver_name', '{{%driver}}', 'name');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%driver}}');
    }
}
