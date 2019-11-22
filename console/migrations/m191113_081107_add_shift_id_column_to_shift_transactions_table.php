<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%shift_transactions}}`.
 */
class m191113_081107_add_shift_id_column_to_shift_transactions_table extends Migration
{
    public $tableName = '{{%shift_transactions}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'shift_id', $this->integer()->after('user_id'));
        $this->addForeignKey(
            'fk-shift-transaction-shift_id-shift_history_id',
            $this->tableName,
            'shift_id',
            \common\models\ShiftHistory::tableName(),
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-shift-transaction-shift_id-shift_history_id', $this->tableName);
        $this->dropColumn($this->tableName, 'shift_id');
    }
}
