<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order}}`.
 */
class m190904_115732_create_order_table extends Migration
{
    public $tableName = '{{%order}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'created_by' => $this->integer(),
            'customer_id' => $this->integer(),
            'cost' => $this->double(),
            'service_cost' => $this->double(),
            'discount_cost' => $this->double(),
            'total_cost' => $this->double(),
            'status' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk-order-created_by-user-id',
            $this->tableName,
            'created_by',
            '{{%user}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
