<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%customer}}`.
 */
class m190904_115230_create_customer_table extends Migration
{
    public $tableName = '{{%customer}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'full_name' => $this->integer(),
            'phone' => $this->integer(),
            'address' => $this->string(),
            'birthday_date' => $this->string(),
            'card_number' => $this->integer(),
            'discount_id' => $this->integer(),
            'is_discount_limited' => $this->boolean(),
            'discount_value' => $this->tinyInteger(),
            'discount_quantity' => $this->integer(),
            'status' => $this->boolean()->notNull()->defaultValue(0),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk-customer-discount_id-discount-id',
            $this->tableName,
            'discount_id',
            '{{%discount}}',
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
