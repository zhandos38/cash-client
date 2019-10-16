<?php


namespace backend\models;


use common\models\Product;
use kartik\daterange\DateRangeBehavior;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class WarehouseSearch extends Product
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
            [['name', 'barcode'], 'string', 'max' => 255],
            [['status', 'company_id', 'is_partial'], 'integer'],
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
        $query = Product::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'status' => $this->status,
            'company_id' => $this->company_id,
            'is_partial' => $this->is_partial
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
              ->andFilterWhere(['like', 'barcode', $this->barcode]);

        if ($this->createTimeRange) {
            $query->andFilterWhere(['>=', 'created_at', $this->createTimeStart+((60*60)*6)])
                ->andFilterWhere(['<', 'created_at', $this->createTimeEnd+((60*60)*6)]);
        }

        return $dataProvider;
    }
}