<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%shift_history}}`.
 */
class m191101_050323_create_shift_history_table extends Migration
{
    public $tableName = '{{%shift_history}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'status' => $this->boolean()->defaultValue(true),
            'started_at' => $this->integer(),
            'closed_at' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk-shift-history-user_id-user-id',
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
        $this->dropForeignKey('fk-shift-history-user_id-user-id', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
