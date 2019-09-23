<?php

use yii\db\Migration;

/**
 * Class m190913_094218_alter_number_in_column_in_invoice_table
 */
class m190913_094218_alter_number_in_column_in_invoice_table extends Migration
{
    public $tableName = '{{invoice}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn($this->tableName, 'number_in', $this->string(22));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn($this->tableName, 'number_in', $this->integer());
    }
}
