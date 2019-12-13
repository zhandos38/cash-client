<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%settings}}`.
 */
class m191212_102527_add_exported_at_column_to_settings_table extends Migration
{
    public $tableName = '{{%settings}}';
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn($this->tableName, 'exported_at', $this->integer());
        $this->addColumn($this->tableName, 'is_updated', $this->boolean()->defaultValue(true));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn($this->tableName, 'exported_at');
        $this->dropColumn($this->tableName, 'is_updated');
    }
}
