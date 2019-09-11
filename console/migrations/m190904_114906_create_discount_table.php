<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%discout}}`.
 */
class m190904_114906_create_discount_table extends Migration
{
    public $tableName = '{{%discount}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'value' => $this->tinyInteger(),
            'quantity' => $this->integer(),
            'is_limited' => $this->boolean(),
            'status' => $this->boolean()->defaultValue(0),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
