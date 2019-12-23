<?php

use yii\db\Migration;

/**
 * Class m191123_074640_add_exported_at_to_product_table
 */
class m191123_074640_add_exported_at_to_product_table extends Migration
{
    public $tableName = '{{%product}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'exported_at', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'exported_at');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191123_074640_add_exported_at_to_product_table cannot be reverted.\n";

        return false;
    }
    */
}
