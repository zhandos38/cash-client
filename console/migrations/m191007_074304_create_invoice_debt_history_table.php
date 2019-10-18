<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%invoice_debt_history}}`.
 */
class m191007_074304_create_invoice_debt_history_table extends Migration
{
    public $tableName = '{{%invoice_debt_history}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'invoice_id' => $this->integer(),
            'paid_amount' => $this->double(),
            'created_at' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk-invoice-invoice_id-invoice-id',
            $this->tableName,
            'invoice_id',
            \common\models\Invoice::tableName(),
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-invoice-invoice_id-invoice-id', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
