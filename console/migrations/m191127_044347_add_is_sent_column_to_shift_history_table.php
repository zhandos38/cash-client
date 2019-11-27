<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%shift_history}}`.
 */
class m191127_044347_add_is_sent_column_to_shift_history_table extends Migration
{
    public $tableName = '{{%shift_history}}';

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
