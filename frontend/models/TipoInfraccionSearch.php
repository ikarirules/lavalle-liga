<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\TipoInfraccion;

/**
 * TipoInfraccionSearch represents the model behind the search form of `common\models\TipoInfraccion`.
 */
class TipoInfraccionSearch extends TipoInfraccion
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'sancion_fechas_min', 'sancion_fechas_max'], 'integer'],
            [['nombre', 'descripcion', 'sancion_descripcion'], 'safe'],
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
     * @param string|null $formName Form name to be used into `->load()` method.
     *
     * @return ActiveDataProvider
     */
    public function search($params, $formName = null)
    {
        $query = TipoInfraccion::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'sancion_fechas_min' => $this->sancion_fechas_min,
            'sancion_fechas_max' => $this->sancion_fechas_max,
        ]);

        $query->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'descripcion', $this->descripcion])
            ->andFilterWhere(['like', 'sancion_descripcion', $this->sancion_descripcion]);

        return $dataProvider;
    }
}
