<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%product}}`.
 */
class m190912_091142_add_company_id_column_to_product_table extends Migration
{
    public $tableName = '{{%product}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn(
            $this->tableName,
            'company_id',
            $this->integer()->after('status')
        );

        $this->addForeignKey(
            'fk-product-company_id-company-id',
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
        $this->dropForeignKey('fk-product-company_id-company-id', $this->tableName);
        $this->dropColumn($this->tableName, 'company_id');
    }
}
