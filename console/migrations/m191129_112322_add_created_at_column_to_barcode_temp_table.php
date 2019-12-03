<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%barcode_temp}}`.
 */
class m191129_112322_add_created_at_column_to_barcode_temp_table extends Migration
{
    public $tableName = '{{%barcode_temp}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'created_at', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'created_at');
    }
}
