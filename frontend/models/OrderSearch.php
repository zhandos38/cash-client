<?php

namespace frontend\models;

use kartik\daterange\DateRangeBehavior;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Order;

/**
 * OrderSearch represents the model behind the search form of `common\models\Order`.
 */
class OrderSearch extends Order
{
    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;
    public $phone;
    public $customer_name;

    public function behaviors()
    {
        return [
            [
                'class' => DateRangeBehavior::className(),
                'attribute' => 'createTimeRange',
                'dateStartAttribute' => 'createTimeStart',
                'dateEndAttribute' => 'createTimeEnd',
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'created_by', 'customer_id', 'cost', 'service_cost', 'discount_cost', 'total_cost', 'status', 'is_debt', 'created_at', 'updated_at', 'company_id'], 'integer'],
            [['phone', 'customer_name'], 'string'],
            [['createTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/']
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
        $query = Order::find()
                ->alias('t1')
                ->with('createdBy')
                ->joinWith('customer t2')
                ->andWhere(['t1.company_id' => \Yii::$app->user->identity->company_id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'created_by' => $this->created_by,
            'customer_id' => $this->customer_id,
            'cost' => $this->cost,
            'service_cost' => $this->service_cost,
            'discount_cost' => $this->discount_cost,
            'total_cost' => $this->total_cost,
            'status' => $this->status,
            'is_debt' => $this->is_debt,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'company_id' => $this->company_id
        ]);

        $query->andFilterWhere(['like', 't2.phone', $this->phone]);
        $query->andFilterWhere(['like', 't2.full_name', $this->customer_name]);

        if ($this->createTimeRange) {
            $query->andFilterWhere(['>=', 'created_at', $this->createTimeStart+((60*60)*6)])
                ->andFilterWhere(['<', 'created_at', $this->createTimeEnd+((60*60)*6)]);
        }

        return $dataProvider;
    }
}
