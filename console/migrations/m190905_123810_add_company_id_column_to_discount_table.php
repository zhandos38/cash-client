<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%discount_list}}`.
 */
class m190905_123810_add_company_id_column_to_discount_table extends Migration
{
    public $tableName = '{{%discount}}';
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
            'fk-discount-company_id-company-id',
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
