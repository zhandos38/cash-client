<?php

use yii\db\Migration;

/**
 * Class m190903_122724_user_table
 */
class m190903_122700_add_columns_to_user_table extends Migration
{
    public $tableName = '{{%user}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'full_name', $this->string()->after('username'));
        $this->addColumn($this->tableName, 'phone', $this->string(11)->after('email'));
        $this->addColumn($this->tableName, 'address', $this->string()->after('phone'));
        $this->addColumn($this->tableName, 'code_number', $this->string(12)->after('address'));
        $this->addColumn($this->tableName, 'role', $this->string()->after('code_number'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'full_name');
        $this->dropColumn($this->tableName, 'phone');
        $this->dropColumn($this->tableName, 'address');
        $this->dropColumn($this->tableName, 'code_number');
        $this->dropColumn($this->tableName, 'role');
    }
}
