<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%customer}}`.
 */
class m191126_075116_add_is_sent_column_to_customer_table extends Migration
{
    public $tableNAme = '{{%customer}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableNAme, 'is_sent', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableNAme, 'is_sent');
    }
}
