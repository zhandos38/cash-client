<?php

namespace frontend\models;

use kartik\daterange\DateRangeBehavior;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Product;

/**
 * ProductSearch represents the model behind the search form of `common\models\Product`.
 */
class ProductSearch extends Product
{
    public $updateTimeRange;
    public $updateTimeStart;
    public $updateTimeEnd;

    public function behaviors()
    {
        return [
            [
                'class' => DateRangeBehavior::className(),
                'attribute' => 'updateTimeRange',
                'dateStartAttribute' => 'updateTimeStart',
                'dateEndAttribute' => 'updateTimeEnd',
            ]
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'quantity', 'price_wholesale', 'price_retail', 'wholesale_value', 'is_partial', 'status', 'created_at', 'updated_at'], 'integer'],
            [['barcode', 'name'], 'safe'],
            [['updateTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Product::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'quantity' => $this->quantity,
            'price_wholesale' => $this->price_wholesale,
            'price_retail' => $this->price_retail,
            'wholesale_value' => $this->wholesale_value,
            'is_partial' => $this->is_partial,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'barcode', $this->barcode])
            ->andFilterWhere(['like', 'name', $this->name]);

        if ($this->updateTimeRange) {
            $query->andFilterWhere(['>=', 'updated_at', $this->updateTimeStart+((60*60)*6)])
                ->andFilterWhere(['<', 'updated_at', $this->updateTimeEnd+((60*60)*6)]);
        }

        return $dataProvider;
    }
}
