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
            'company_id',
            $this->integer()
        );

        $this->addForeignKey(
            'fk-invoice-company_id-company-id',
            $this->tableName,
            'company_id',
            '{{%company}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'company_id');
    }
}
