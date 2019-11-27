<?php

use yii\db\Migration;

/**
 * Class m191127_035345_add_exported_at_to_supplier_table
 */
class m191127_035345_add_exported_at_to_supplier_table extends Migration
{
    public $tableName = '{{%supplier}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'exported_at', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'exported_at');
    }
}
