<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "barcode".
 *
 * @property int $id
 * @property int $number
 * @property string $name
 * @property string $img
 */
class Barcode extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'barcode';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number'], 'integer'],
            [['name', 'img'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Number',
            'name' => 'Name',
            'img' => 'Img',
        ];
    }
}
