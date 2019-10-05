<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%product}}`.
 */
class m190904_110930_create_product_table extends Migration
{
    public $tableName = '{{%product}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'barcode' => $this->string(),
            'name' => $this->string(),
            'quantity' => $this->double(),
            'price_wholesale' => $this->double(),
            'price_retail' => $this->double(),
            'wholesale_value' => $this->integer(),
            'is_partial' => $this->boolean(),
            'status' => $this->boolean()->defaultValue(0),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
