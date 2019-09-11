<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%customer}}`.
 */
class m190905_123705_add_company_id_column_to_customer_table extends Migration
{
    public $tableName = '{{%customer}}';
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
            'fk-customer-company_id-company-id',
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
