<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $name
 * @property int $parent_id
 * @property int $color_id
 * @property int $is_active
 * @property int $created_at
 * @property int $updated_at
 */
class Category extends \yii\db\ActiveRecord
{
    const STATUS_NOT_ACTIVE = 0;
    const STATUS_ACTIVE = 1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className()
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id', 'is_active', 'created_at', 'updated_at'], 'integer'],
            [['color_id'], 'string'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'parent_id' => 'Родитель',
            'color_id' => 'Цвет',
            'is_active' => 'Статус',
            'created_at' => 'Добавлено в',
            'updated_at' => 'Обновлено в',
        ];
    }

    public function getChildren()
    {
        return $this->hasMany(Category::className(), ['parent_id' => 'id']);
    }


    public function getParent()
    {
        return $this->hasOne(Category::className(), ['id' => 'parent_id']);
    }

    public static function getCategoryLabels()
    {
        return [
            self::STATUS_NOT_ACTIVE => 'Отключен',
            self::STATUS_ACTIVE => 'Включен'
        ];
    }

    public function getCategoryLabel()
    {
        return ArrayHelper::getValue(self::getCategoryLabels(), $this->is_active);
    }
}
