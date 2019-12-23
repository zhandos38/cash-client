<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
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
 * @property int $percentage_rate
 *
 * @property OrderItems[] $orderItems
 * @property bool $is_favourite [tinyint(1)]
 * @property int $exported_at [int(11)]
 * @property bool $is_sent [tinyint(1)]
 * @property int $category_id [int(11)]
 */
class Product extends \yii\db\ActiveRecord
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    const IS_FAVOURITE_NO = 0;
    const IS_FAVOURITE_YES = 1;

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
            [['wholesale_value', 'is_partial', 'status', 'created_at', 'updated_at', 'exported_at', 'is_sent', 'exported_at', 'category_id'], 'integer'],
            [['quantity', 'price_wholesale', 'price_retail', 'percentage_rate'], 'number'],
            [['barcode', 'name'], 'string', 'max' => 255],
            ['is_favourite', 'boolean']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Выберите категорию',
            'barcode' => 'Штрих код',
            'name' => 'Наименование товара',
            'quantity' => 'Количество (Остаток на складе)',
            'price_wholesale' => 'Оптовая цена',
            'price_retail' => 'Розничная цена',
            'wholesale_value' => 'Оптовое количество',
            'is_partial' => 'Частичный',
            'status' => 'Статус',
            'is_favourite' => 'Избранный',
            'created_at' => 'Дата добавления',
            'updated_at' => 'Дата обновления',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderItems()
    {
        return $this->hasMany(OrderItems::className(), ['product_id' => 'id']);
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

    public static function getIsFavouriteLabels()
    {
        return [
            0 => 'Нет',
            1 => 'Да'
        ];
    }

    /**
     * @return mixed
     */
    public function getIsFavouriteLabel()
    {
        return ArrayHelper::getValue(static::getBooleanStatuses(), $this->is_partial);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            if (!empty($changedAttributes['status']) && $changedAttributes['status'] <> $this->status) {
                if ($changedAttributes['status'] == self::STATUS_INACTIVE) {
                    ElasticProduct::addProductById($this->id);
                } elseif ($changedAttributes['status'] == self::STATUS_ACTIVE) {
                    ElasticProduct::deleteProductById($this->id);
                }
            }

            if (!empty($changedAttributes['quantity']) && $changedAttributes['quantity'] <> $this->quantity) {

                if ($this->quantity <= 0 || $this->quantity == 0) {
                    ElasticProduct::deleteProductById($this->id);
                } elseif ($this->quantity > 0 && !ElasticProduct::findProductById($this->id)) {
                    ElasticProduct::addProductById($this->id);
                }
            }

            if (!empty($changedAttributes['category_id']) && $changedAttributes['category_id'] <> $this->category_id) {
                ElasticProduct::setProductCategory($this->id, (int)$this->category_id);
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
