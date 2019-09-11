<?php

use yii\db\Migration;

/**
 * Class m190904_100538_supplier_table
 */
class m190904_100538_supplier_table extends Migration
{
    public $tableName = '{{%supplier}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName,
            [
                'id' => $this->primaryKey(),
                'name' => $this->string(),
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
