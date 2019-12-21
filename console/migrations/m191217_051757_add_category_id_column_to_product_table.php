<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%product}}`.
 */
class m191217_051757_add_category_id_column_to_product_table extends Migration
{
    public $tableName = '{{%product}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'category_id', $this->integer()->after('name'));

        $this->addForeignKey(
            'fk-product-category_id-category-id',
            $this->tableName,
            'category_id',
            '{{category}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-product-category_id-category-id', $this->tableName);

        $this->dropColumn($this->tableName, 'category_id');
    }
}
