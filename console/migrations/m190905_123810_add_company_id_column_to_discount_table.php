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
            'object_id',
            $this->integer()
        );

        $this->addForeignKey(
            'fk-discount-object_id-company_objects-id',
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
        $this->dropForeignKey('fk-discount-object_id-company_object-id', $this->tableName);
        $this->dropColumn($this->tableName, 'object_id');
    }
}
