<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shift_transactions}}`.
 */
class m191101_101455_create_shift_transactions_table extends Migration
{
    public $tableName = '{{%shift_transactions}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'sum' => $this->float(),
            'type_id' => $this->integer(),
            'user_id' => $this->integer(),
            'comment' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk-shift-transactions-user_id-user-id',
            $this->tableName,
            'user_id',
            \common\models\User::tableName(),
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'fk-shift-transactions-user_id-user-id');
        $this->dropTable($this->tableName);
    }
}
