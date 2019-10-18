<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%barcode_temp}}`.
 */
class m190905_125739_create_barcode_temp_table extends Migration
{
    public $tableName = '{{%barcode_temp}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'number' => $this->string(22),
            'name' => $this->string(),
            'img' => $this->string(),
            'object_id' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk-barcode-temp-object_id-company_objects-id',
            $this->tableName,
            'object_id',
            '{{%company_objects}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-barcode-temp-object_id-company_objects-id', $this->tableName);
        $this->dropTable($this->tableName);
    }
}
