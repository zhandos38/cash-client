<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%order_debt_history}}`.
 */
class m191009_083044_create_order_debt_history_table extends Migration
{
    public $tableName = '{{%order_debt_history}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'order_id' => $this->integer(),
            'paid_amount' => $this->double(),
            'created_at' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk-order-debt-history-order_id-order-id',
            $this->tableName,
            'order_id',
            \common\models\Order::tableName(),
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
