<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%order}}`.
 */
class m191118_042249_add_pay_status_column_to_order_table extends Migration
{
    public $tableName = '{{%Order}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'pay_status', $this->tinyInteger(3)->after('pay_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'pay_status');
    }
}
