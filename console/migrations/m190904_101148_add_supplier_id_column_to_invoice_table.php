<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%invoice}}`.
 */
class m190904_101148_add_supplier_id_column_to_invoice_table extends Migration
{
    public $tableName = '{{invoice}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'supplier_id', $this->integer());

        $this->addForeignKey(
            'fk-invoice-supplier_id-supplier-id',
            $this->tableName,
            'supplier_id',
            '{{supplier}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'supplier_id');
        $this->dropForeignKey('fk-invoice-supplier_id', $this->tableName);
    }
}
