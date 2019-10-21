<?php

use yii\db\Migration;

/**
 * Class m190904_100100_invoice_table
 */
class m190904_100100_invoice_table extends Migration
{
    public $tableName = '{{%invoice}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName,
            [
                'id' => $this->primaryKey(),
                'number_in' => $this->string(22),
                'is_debt' => $this->boolean()->defaultValue(0),
                'status' => $this->boolean()->defaultValue(0),
                'created_by' => $this->integer(),
                'created_at' => $this->integer()
            ]);

        $this->addForeignKey(
            'fk-invoice-created_by-user-id',
            $this->tableName,
            'created_by',
            '{{%user}}',
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
