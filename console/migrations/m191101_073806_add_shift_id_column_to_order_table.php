<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%order}}`.
 */
class m191101_073806_add_shift_id_column_to_order_table extends Migration
{
    public $tableName = '{{%order}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName,'shift_id', $this->integer()->after('is_debt'));
        $this->addForeignKey(
            'fk-order-shift_id-shift-history-id',
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
        $this->dropForeignKey('fk-order-shift_id-shift-history-id', $this->tableName);
        $this->dropColumn($this->tableName, 'shift_id');
    }
}
