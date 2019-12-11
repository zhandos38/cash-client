<?php

namespace frontend\models;

use common\models\ShiftHistory;
use kartik\daterange\DateRangeBehavior;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Supplier;

/**
 * SupplierSearch represents the model behind the search form of `common\models\Supplier`.
 */
class ShiftSearch extends ShiftHistory
{
    public $startTimeRange;
    public $startTimeStart;
    public $startTimeEnd;

    public $closeTimeRange;
    public $closeTimeStart;
    public $closeTimeEnd;

    public function behaviors()
    {
        return [
            [
                'class' => DateRangeBehavior::className(),
                'attribute' => 'startTimeRange',
                'dateStartAttribute' => 'startTimeStart',
                'dateEndAttribute' => 'startTimeEnd',
            ],
            [
                'class' => DateRangeBehavior::className(),
                'attribute' => 'closeTimeRange',
                'dateStartAttribute' => 'closeTimeStart',
                'dateEndAttribute' => 'closeTimeEnd',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'sum_at_close'], 'integer'],
            [['is_sent', 'status'], 'boolean'],
            [['startTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/'],
            [['closeTimeRange'], 'match', 'pattern' => '/^.+\s\-\s.+$/']
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
        $query = ShiftHistory::find();

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
            'status' => $this->status,
            'user_id' => $this->user_id
        ]);

        if ($this->startTimeRange) {
            $query->andFilterWhere(['>=', 'started_at', $this->startTimeStart+((60*60)*6)])
                ->andFilterWhere(['<', 'started_at', $this->startTimeEnd+((60*60)*6)]);
        }

        if ($this->closeTimeRange) {
            $query->andFilterWhere(['>=', 'closed_at', $this->closeTimeStart+((60*60)*6)])
                ->andFilterWhere(['<', 'closed_at', $this->closeTimeEnd+((60*60)*6)]);
        }

        return $dataProvider;
    }
}
