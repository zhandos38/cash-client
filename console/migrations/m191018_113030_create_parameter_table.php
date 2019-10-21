<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%parameter}}`.
 */
class m191018_113030_create_parameter_table extends Migration
{
    public $tableName = '{{%parameter}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'value_int' => $this->integer(),
            'value_str' => $this->string()
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
