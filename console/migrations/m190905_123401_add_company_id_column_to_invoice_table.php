<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%invoice}}`.
 */
class m190905_123401_add_company_id_column_to_invoice_table extends Migration
{
    public $tableName = '{{%invoice}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            $this->tableName,
            'object_id',
            $this->integer()
        );

        $this->addForeignKey(
            'fk-invoice-object_id-company_objects-id',
            $this->tableName,
            'object_id',
            '{{%company_objects}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-invoice-object_id-company_objects-id', $this->tableName);
        $this->dropColumn($this->tableName, 'object_id');
    }
}
