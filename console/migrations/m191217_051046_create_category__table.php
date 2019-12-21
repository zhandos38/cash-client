<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%category_}}`.
 */
class m191217_051046_create_category__table extends Migration
{
    public $tableName = '{{%category}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'parent_id' => $this->integer(),
            'color_id' => $this->string(),
            'is_active' => $this->boolean(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
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
