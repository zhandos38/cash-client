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
            'company_id',
            $this->integer()
        );

        $this->addForeignKey(
            'fk-staff-company_id-company-id',
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
