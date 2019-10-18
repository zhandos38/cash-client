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
            'object_id',
            $this->integer()
        );

        $this->addForeignKey(
            'fk-order-object_id-object-id',
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
        $this->dropForeignKey('fk-order-object_id-company_objects-id', $this->tableName);
        $this->dropColumn($this->tableName, 'object_id');
    }
}
