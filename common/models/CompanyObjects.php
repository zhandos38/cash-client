<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "company_objects".
 *
 * @property int $id
 * @property int $company_id
 * @property int $type_id
 * @property string $name
 * @property string $address
 */
class CompanyObjects extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'company_objects';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['company_id', 'type_id'], 'integer'],
            [['name', 'address'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'company_id' => 'Company ID',
            'type_id' => 'Type ID',
            'name' => 'Name',
            'address' => 'Address',
        ];
    }
}
