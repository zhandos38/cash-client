<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%barcode_temp}}`.
 */
class m191129_112156_add_is_sent_column_to_barcode_temp_table extends Migration
{
    public $tableName = '{{%barcode_temp}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'is_sent', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'is_sent');
    }
}
