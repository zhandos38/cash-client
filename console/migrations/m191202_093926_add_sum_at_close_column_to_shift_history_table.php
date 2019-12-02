<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%shift_history}}`.
 */
class m191202_093926_add_sum_at_close_column_to_shift_history_table extends Migration
{
    public $tableName = '{{%shift_history}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'sum_at_close', $this->float()->after('status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'sum_at_close');
    }
}
