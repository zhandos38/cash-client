<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%barcode}}`.
 */
class m190905_125316_create_barcode_table extends Migration
{
    public $tableName = '{{%barcode}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'number' => $this->integer(),
            'name' => $this->string(),
            'img' => $this->string()
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
