<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%company_objects}}`.
 */
class m190905_090820_create_company_objects_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%company_objects}}', [
            'id' => $this->primaryKey(),
            'company_id' => $this->integer(),
            'type_id' => $this->integer(),
            'name' => $this->string(),
            'address' => $this->string(),
            'status' => $this->boolean()->defaultValue(0),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer()
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%company_objects}}');
    }
}
