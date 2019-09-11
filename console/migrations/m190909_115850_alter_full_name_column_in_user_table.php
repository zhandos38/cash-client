<?php

use yii\db\Migration;

/**
 * Class m190909_115850_change_full_name_type_in_user_table
 */
class m190909_115850_alter_full_name_column_in_user_table extends Migration
{
    public $tableName = '{{%user}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn($this->tableName, 'full_name', $this->string());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190909_115850_change_full_name_type_in_user_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190909_115850_change_full_name_type_in_user_table cannot be reverted.\n";

        return false;
    }
    */
}
