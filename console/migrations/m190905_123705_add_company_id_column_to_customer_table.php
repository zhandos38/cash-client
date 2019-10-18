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
            'object_id',
            $this->integer()
        );

        $this->addForeignKey(
            'fk-customer-object_id-company_objects-id',
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
        $this->dropForeignKey('fk-customer-object_id-object-id', $this->tableName);
        $this->dropColumn($this->tableName, 'object_id');
    }
}
