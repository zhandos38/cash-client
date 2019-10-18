<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%staff}}`.
 */
class m190905_123049_add_company_id_column_to_user_table extends Migration
{
    public $tableName = '{{%user}}';
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
            'fk-staff-object_id-company_objects-id',
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
        $this->dropForeignKey('fk-staff-object_id-company_objects-id', $this->tableName);
        $this->dropColumn($this->tableName, 'object_id');
    }
}
