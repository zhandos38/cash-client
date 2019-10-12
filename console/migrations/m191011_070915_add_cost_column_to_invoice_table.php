<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%invoice}}`.
 */
class m191011_070915_add_cost_column_to_invoice_table extends Migration
{
    public $tableName = '{{%invoice}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'cost', $this->double());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'cost');
    }
}
