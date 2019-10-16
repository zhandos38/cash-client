<?php


namespace backend\models;


use common\models\Invoice;
use kartik\daterange\DateRangeBehavior;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class InvoiceSearch extends Invoice
{
    public $createTimeRange;
    public $createTimeStart;
    public $createTimeEnd;

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
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'company_id'], 'integer'],
            [['number_in'], 'string', 'max' => 255],
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
        $query = Invoice::find()
                ->with('company');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'is_debt' => $this->is_debt,
            'status' => $this->status,
            'company_id' => $this->company_id
        ]);

        $query->andFilterWhere(['like', 'number_in', $this->number_in]);

        if ($this->createTimeRange) {
            $query->andFilterWhere(['>=', 'created_at', $this->createTimeStart+((60*60)*6)])
                ->andFilterWhere(['<', 'created_at', $this->createTimeEnd+((60*60)*6)]);
        }

        return $dataProvider;
    }
}