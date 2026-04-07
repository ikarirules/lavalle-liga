<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\InformeDetalle;

/**
 * InformeDetalleSearch represents the model behind the search form of `common\models\InformeDetalle`.
 */
class InformeDetalleSearch extends InformeDetalle
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'informe_id', 'minuto', 'jugador_id', 'numero_camiseta', 'club_id', 'tipo_infraccion_id', 'created_at', 'updated_at'], 'integer'],
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
        $query = InformeDetalle::find();

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
            'informe_id' => $this->informe_id,
            'minuto' => $this->minuto,
            'jugador_id' => $this->jugador_id,
            'numero_camiseta' => $this->numero_camiseta,
            'club_id' => $this->club_id,
            'tipo_infraccion_id' => $this->tipo_infraccion_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        return $dataProvider;
    }
}
