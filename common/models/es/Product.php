<?php
namespace common\models\es;

use Yii;
use yii\elasticsearch\ActiveRecord;

/**
 * Class Product
 *
 * @property integer $id
 * @property string $name
 *
 * @package common\models\es
 * @property Product $product
 */
class Product extends ActiveRecord
{
    public static function index()
    {
        return 'my_first_second';
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
            'barcode'
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
}