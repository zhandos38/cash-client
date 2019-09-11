<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%invoice_items}}`.
 */
class m190904_101919_create_invoice_items_table extends Migration
{
    public $tableName = '{{%invoice_items}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'invoice_id' => $this->integer(),
            'barcode' => $this->string(),
            'name' => $this->string(),
            'quantity' => $this->integer(),
            'price_in' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk-invoice-items-invoice_id-invoice-id',
            $this->tableName,
            'invoice_id',
            '{{%invoice}}',
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
