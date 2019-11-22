<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%order_items}}`.
 */
class m191101_105557_add_status_column_to_order_items_table extends Migration
{
    public $tableName = '{{%order_items}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'status', $this->tinyInteger(3)->after('real_price'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'status');
    }
}
