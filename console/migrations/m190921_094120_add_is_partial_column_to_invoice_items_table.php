<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%invoice_items}}`.
 */
class m190921_094120_add_is_partial_column_to_invoice_items_table extends Migration
{
    public $tableName = "{{%invoice_items}}";
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'is_partial', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'is_partial');
    }
}
