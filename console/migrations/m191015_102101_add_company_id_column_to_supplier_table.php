<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%supplier}}`.
 */
class m191015_102101_add_company_id_column_to_supplier_table extends Migration
{
    public $tableName = '{{%supplier}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'company_id', $this->integer()->after('name'));
        $this->addForeignKey(
            'fk-supplier-company_id-company-id',
            $this->tableName,
            'company_id',
            \common\models\Company::tableName(),
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-supplier-company_id-company-id', $this->tableName);
        $this->dropColumn($this->tableName, 'company_id');
    }
}
