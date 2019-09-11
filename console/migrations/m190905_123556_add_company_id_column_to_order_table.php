<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%order}}`.
 */
class m190905_123556_add_company_id_column_to_order_table extends Migration
{
    public $tableName = '{{%order}}';
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
            'fk-order-company_id-company-id',
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
