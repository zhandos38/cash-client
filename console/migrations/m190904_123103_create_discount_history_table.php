<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%discount_history}}`.
 */
class m190904_123103_create_discount_history_table extends Migration
{
    public $tableName = '{{%discount_history}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer(),
            'customer_id' => $this->integer(),
            'discount_value' => $this->integer(),
            'created_at' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk-discount-history-order_id-order-id',
            $this->tableName,
            'order_id',
            '{{%order}}',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-discount-history-customer_id-customer-id',
            $this->tableName,
            'customer_id',
            '{{%customer}}',
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
