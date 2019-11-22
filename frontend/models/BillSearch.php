<?php


namespace frontend\models;


use common\models\Order;
use yii\base\Model;
use yii\base\UserException;
use yii\data\ActiveDataProvider;

class BillSearch extends Order
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number', 'cost', 'pay_id', 'status'], 'integer']
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
     * @throws UserException
     */
    public function search($params)
    {
        $shift_id = \Yii::$app->object->getShiftId();

        if (!$shift_id)
            throw new UserException('Смена не установалена');

        $query = Order::find()
            ->andWhere(['shift_id' => $shift_id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20
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
            'number' => $this->number,
            'status' => $this->status,
            'pay_id' => $this->pay_id,
            'cost' => $this->cost,
            'created_at' => $this->created_at
        ]);

        return $dataProvider;
    }
}
