<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%log}}`.
 */
class m191123_080329_create_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%log}}', [
            'id' => $this->primaryKey(),
            'source_id' => $this->integer(),
            'message' => $this->text(),
            'started_at' => $this->integer(),
            'finished_at' => $this->integer(),
            'status' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%log}}');
    }
}
