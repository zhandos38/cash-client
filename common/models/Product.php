<?php

namespace common\models;

use Yii;
use yii\base\UserException;
use yii\behaviors\TimestampBehavior;
use yii\console\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use common\models\es\Product as ElasticProduct;

/**
 * This is the model class for table "product".
 *
 * @property int $id
 * @property string $barcode
 * @property string $name
 * @property int $quantity
 * @property int $price_wholesale
 * @property int $price_retail
 * @property int $wholesale_value
 * @property int $is_partial
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $company_id
 * @property int $percentage_rate
 *
 * @property OrderItems[] $orderItems
 */
class Product extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'product';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['wholesale_value', 'is_partial', 'status', 'created_at', 'updated_at', 'percentage_rate'], 'integer'],
            [['quantity', 'price_wholesale', 'price_retail'], 'number'],
            [['barcode', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'barcode' => 'Штрих код',
            'name' => 'Название',
            'quantity' => 'Количество',
            'price_wholesale' => 'Цена оптовая',
            'price_retail' => 'Цена розничная',
            'wholesale_value' => 'Оптом',
            'is_partial' => 'Частичный',
            'status' => 'Статус',
            'company_id' => 'Компания',
            'created_at' => 'Дата добавление',
            'updated_at' => 'Дата обновление',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItems::className(), ['product_id' => 'id']);
    }

    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    public static function getStatuses() {
        return [
            self::STATUS_INACTIVE => 'Отключен',
            self::STATUS_ACTIVE => 'Включен'
        ];
    }

    /**
     * @return mixed
     */
    public function getStatusLabel()
    {
        return ArrayHelper::getValue(static::getStatuses(), $this->status);
    }

    public static function getBooleanStatuses()
    {
        return [
            0 => 'Нет',
            1 => 'Да'
        ];
    }

    /**
     * @return mixed
     */
    public function getBooleanStatus()
    {
        return ArrayHelper::getValue(static::getBooleanStatuses(), $this->is_partial);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            if (!$changedAttributes['quantity']) {
                if ($changedAttributes['status'] == self::STATUS_INACTIVE) {
                    ElasticProduct::addProductById($this->id);
                } elseif ($changedAttributes['status'] == self::STATUS_ACTIVE) {
                    ElasticProduct::deleteProductById($this->id);
                }
            }

        } else {
            $elasticProduct = ElasticProduct::find()->where(['id' => $this->id])->one();
            if (!$elasticProduct) {
                ElasticProduct::addProductById($this->id);
            }
        }
        parent::afterSave($insert, $changedAttributes);
    }
}
