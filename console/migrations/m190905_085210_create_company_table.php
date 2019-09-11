<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%company}}`.
 */
class m190905_085210_create_company_table extends Migration
{
    public $tableName = '{{%company}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable($this->tableName, [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'iin' => $this->string(12),
            'address_legal' => $this->string(),
            'address_actual' => $this->string(),
            'ceo' => $this->string(),
            'contact_person' => $this->string(),
            'phone' => $this->string(),
            'balance' => $this->double(),
            'manager_id' => $this->integer(),
            'status' => $this->boolean()->defaultValue(0),
            'expired_at' => $this->integer(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer()
        ]);

        $this->addForeignKey(
            'fk-company-manager_id-user-id',
            $this->tableName,
            'manager_id',
            '{{%user}}',
            'id',
            'SET NULL'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable($this->tableName);
    }
}
