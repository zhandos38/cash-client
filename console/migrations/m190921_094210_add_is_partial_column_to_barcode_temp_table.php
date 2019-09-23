<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%barcode_temp}}`.
 */
class m190921_094210_add_is_partial_column_to_barcode_temp_table extends Migration
{
    public $tableName = "{{%barcode_temp}}";
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'is_partial', $this->boolean());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'is_partial');
    }
}
