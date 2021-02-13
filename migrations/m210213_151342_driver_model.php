<?php

use yii\db\Migration;

/**
 * Class m210213_151342_driver_model
 */
class m210213_151342_driver_model extends Migration
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

        $this->createTable('{{%driver_model}}', [
            'driver_id' => $this->integer()->notNull(),
            'model_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk_driver_model', '{{%driver_model}}', ['driver_id', 'model_id']);

        $this->addForeignKey(
            'driver_model_to_driver_fk',
            '{{%driver_model}}',
            'driver_id',
            '{{%driver}}',
            'id',
            'CASCADE',
            'RESTRICT'
        );

        $this->addForeignKey(
            'driver_model_to_model_fk',
            '{{%driver_model}}',
            'model_id',
            '{{%model}}',
            'id',
            'CASCADE',
            'RESTRICT'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%driver_model}}');
    }
}
