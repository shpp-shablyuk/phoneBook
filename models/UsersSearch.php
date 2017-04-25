<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * UsersSearch represents the model behind the search form about `app\models\Users`.
 */
class UsersSearch extends Users
{
	public $city;
	public $country;
	public $phoneList;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['city', 'country'], 'string'],
            [['phones'], 'integer'],
            [['fio', 'phoneList'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = Users::find();
	    $query->joinWith([ 'country', 'phones'], true);

        // add conditions that should always apply here
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10]
        ]);

	    $dataProvider->setSort([
		    'attributes' => [
			    'fio',
			    'country' => [
				    'asc' => ['countries.name' => SORT_ASC],
				    'desc' => ['countries.name' => SORT_DESC],
				    'label' => 'countries.name',
				    'default' => SORT_ASC
			    ],
			    'city' => [
				    'asc' => ['cities.name' => SORT_ASC],
				    'desc' => ['cities.name' => SORT_DESC],
				    'label' => 'cities.name',
				    'default' => SORT_ASC
			    ],
			    'phoneList' => [
				    'asc' => ['phones.phone' => SORT_ASC],
				    'desc' => ['phones.phone' => SORT_DESC],
				    'label' => 'phones.phone',
				    'default' => SORT_ASC
			    ]
		    ]
	    ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
	    $query->andFilterWhere(['like', 'phones.phone', $this->phoneList]);
	    $query->andFilterWhere(['like', 'fio', $this->fio]);
        $query->andFilterWhere(['like', 'cities.name', $this->city]);
        $query->andFilterWhere(['like', 'countries.name', $this->country]);
        $query->groupBy('users.id');

        return $dataProvider;
    }
}
