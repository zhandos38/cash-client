<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "barcode_temp".
 *
 * @property int $id
 * @property int $number
 * @property string $name
 * @property string $img
 * @property int $is_partial
 *
 * @property Company $company
 */
class BarcodeTemp extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'barcode_temp';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number'], 'integer'],
            ['is_partial', 'boolean'],
            [['name', 'img'], 'string', 'max' => 255]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'number' => 'Номер',
            'name' => 'Названия'
        ];
    }
}
