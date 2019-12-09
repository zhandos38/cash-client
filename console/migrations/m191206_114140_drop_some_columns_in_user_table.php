<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%some_columns_in_user}}`.
 */
class m191206_114140_drop_some_columns_in_user_table extends Migration
{
    public $tableName = '{{%user}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn($this->tableName, 'username');
        $this->dropColumn($this->tableName, 'email');
        $this->dropColumn($this->tableName, 'address');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "This migrate can't revert";
    }
}
