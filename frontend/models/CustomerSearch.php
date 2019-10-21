<?php

namespace frontend\models;

use kartik\daterange\DateRangeBehavior;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Customer;

/**
 * CustomerSearch represents the model behind the search form of `common\models\Customer`.
 */
class CustomerSearch extends Customer
{
    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;

    public $birthTimeRange;
    public $birthTimeStart;
    public $birthTimeEnd;

    public function behaviors()
    {
        return [
            [
                'class' => DateRangeBehavior::className(),
                'attribute' => 'createTimeRange',
                'dateStartAttribute' => 'createTimeStart',
                'dateEndAttribute' => 'createTimeEnd',
            ],
            [
                'class' => DateRangeBehavior::className(),
                'attribute' => 'birthTimeRange',
                'dateStartAttribute' => 'birthTimeStart',
                'dateEndAttribute' => 'birthTimeEnd',
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'birthday_date', 'card_number', 'discount_id', 'is_discount_limited', 'discount_value', 'discount_quantity', 'status', 'created_at', 'updated_at'], 'integer'],
            [['full_name', 'phone', 'address'], 'safe'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['birthTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/']
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
        $query = Customer::find();

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
            'birthday_date' => $this->birthday_date,
            'card_number' => $this->card_number,
            'discount_id' => $this->discount_id,
            'is_discount_limited' => $this->is_discount_limited,
            'discount_value' => $this->discount_value,
            'discount_quantity' => $this->discount_quantity,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ]);

        $query->andFilterWhere(['like', 'full_name', $this->full_name])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'address', $this->address]);

        if ($this->createTimeRange) {
            $query->andFilterWhere(['>=', 'created_at', $this->createTimeStart+((60*60)*6)])
                ->andFilterWhere(['<', 'created_at', $this->createTimeEnd+((60*60)*6)]);
        }

        if ($this->birthTimeRange) {
            $query->andFilterWhere(['>=', 'created_at', $this->birthTimeStart+((60*60)*6)])
                ->andFilterWhere(['<', 'created_at', $this->birthTimeEnd+((60*60)*6)]);
        }

        return $dataProvider;
    }
}
