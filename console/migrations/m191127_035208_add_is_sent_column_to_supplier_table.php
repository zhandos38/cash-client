<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%supplier}}`.
 */
class m191127_035208_add_is_sent_column_to_supplier_table extends Migration
{
    public $tableName = '{{%supplier}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'is_sent', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'is_sent');
    }
}
