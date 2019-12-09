<?php

use yii\db\Migration;

/**
 * Handles the dropping of table `{{%password_hash_column_in_user}}`.
 */
class m191206_115636_drop_password_hash_column_in_user_table extends Migration
{
    public $tableName = '{{%user}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn($this->tableName,'password_hash');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "This migrate can't revert";
    }
}
