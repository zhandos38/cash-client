<?php
namespace common\models\es;

use common\models\es\Product as ElasticProduct;
use Yii;
use yii\console\Exception;
use yii\elasticsearch\ActiveRecord;

/**
 * Class Product
 *
 * @property integer $id
 * @property string $name
 * @property string $barcode
 * @property string $category_id
 *
 * @package common\models\es
 * @property Product $product
 */
class Product extends ActiveRecord
{
    public static function index()
    {
        return 'local_products';
    }

    public static function type()
    {
        return 'product';
    }

    public function attributes()
    {
        return [
            'id',
            'name',
            'barcode',
            'category_id'
        ];
    }

    /**
     * @return array
     */
    public static function settingsProd()
    {
        return [
            'analysis' => [
                'analyzer' => [
                    'edge_ngram_analyzer' => [
                        'filter' => ['lowercase'],
                        'tokenizer' => 'edge_ngram_tokenizer'
                    ],
                ],
                'tokenizer' => [
                    'edge_ngram_tokenizer' => [
                        'type' => 'edge_ngram',
                        'min_gram' => 3,
                        'max_gram' => 5,
                        'token_chars' => [
                            'letter',
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public static function mappingProd()
    {
        return [
            static::type() => [
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'name' => [
                        'type' => 'text',
                        'fields' => [
                            'completion' => [
                                'type' => 'completion'
                            ]
                        ],
                        'analyzer' => 'standard'
                    ],
                    'barcode' => [
                        'type' => 'text'
                    ]
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public static function settings()
    {
        return [
            'settings' => [
                'analysis' => [
                    'analyzer' => [
                        'ngram_analyzer' => [
                            'filter' => ['lowercase'],
                            'tokenizer' => 'edge_ngram_tokenizer'
                        ],
                    ],
                    'tokenizer' => [
                        'edge_ngram_tokenizer' => [
                            'type' => 'edge_ngram',
                            'min_gram' => 1,
                            'max_gram' => 20,
                            'token_chars' => [
                                'letter',
                                'digit'
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }

    /**
     * @return array
     */
    public static function mapping()
    {
        return [
            static::type() => [
                'properties' => [
                    'id' => [
                        'type' => 'integer'
                    ],
                    'name' => [
                        'type' => 'text',
                        'fields' => [
                            'completion' => [
                                'type' => 'completion'
                            ]
                        ],
                        'analyzer' => 'ngram_analyzer'
                    ],
                    'barcode' => [
                        'type' => 'text',
                        'fields' => [
                            'completion' => [
                                'type' => 'completion'
                            ]
                        ],
                        'analyzer' => 'ngram_analyzer'
                    ]
                ]
            ]
        ];
    }

    /**
     *
     */
    public static function updateMapping()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->setMapping(static::index(), static::type(), static::mapping(), ['include_type_name' => 'true']);
    }

    /**
     * Create index
     */
    public static function createIndex()
    {
        $db = static::getDb();
        $command = $db->createCommand();

        if ($command->indexExists(static::index())) {
            $command->updateAnalyzers(static::index(), static::settings());
        } else {
            $command->createIndex(static::index(), static::settings());
        }
        static::updateMapping();
    }

    /**
     * Delete index
     */
    public static function deleteIndex()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->deleteIndex(static::index(), static::type());
    }

    /**
     * @return \yii\db\ActiveQueryInterface
     */
    public function getProduct()
    {
        return $this->hasOne(\common\models\Product::class, ['id' => 'id']);
    }

    public static function addProductById($product_id)
    {
        if (!$product = \common\models\Product::findOne(['id' => $product_id])) {
            return false;
        }

        $esProduct = new ElasticProduct();
        $esProduct->primaryKey = $product->id;
        $esProduct->id = $product->id;
        $esProduct->name = $product->name;
        $esProduct->category_id = 0;
        $esProduct->save();
    }

    public static function deleteProductById($product_id)
    {
        if ($esProduct = ElasticProduct::find()->andWhere(['id' => $product_id])->one()) {
            $esProduct->delete();
        } else {
            return false;
        }
    }

    public static function setProductCategory($product_id, $category_id)
    {
        if ($esProduct = ElasticProduct::find()->andWhere(['id' => $product_id])->one()) {
            $esProduct->category_id = $category_id;
            return $esProduct->save();
        } else {
            return false;
        }
    }

    public static function findProductById($product_id)
    {
        $esProduct = ElasticProduct::find()->andWhere(['id' => $product_id])->one();
        if ($esProduct)
            return true;
        else
            return false;
    }
}