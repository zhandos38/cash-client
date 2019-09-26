<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order_items}}`.
 */
class m190904_121212_create_order_items_table extends Migration
{
    public $tableName = '{{%order_items}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'product_id' => $this->integer(),
            'order_id' => $this->integer(),
            'name' => $this->string(),
            'barcode' => $this->string(),
            'quantity' => $this->integer(),
            'real_price' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'took_at' => $this->integer(),
            'finished_at' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk-product-items-order_id-order-id',
            $this->tableName,
            'order_id',
            '{{%order}}',
            'id',
            'SET NULL'
        );

        $this->addForeignKey(
            'fk-product-items-product_id-product-id',
            $this->tableName,
            'product_id',
            '{{%product}}',
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
