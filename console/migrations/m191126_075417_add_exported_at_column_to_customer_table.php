<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%customer}}`.
 */
class m191126_075417_add_exported_at_column_to_customer_table extends Migration
{
    public $tableNAme = '{{%customer}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableNAme, 'exported_at', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableNAme, 'exported_at');
    }
}
