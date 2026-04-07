<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Fechas;

/**
 * FechasSearch represents the model behind the search form of `common\models\Fechas`.
 */
class FechasSearch extends Fechas
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'numero_fecha', 'torneo_id', 'club_local_id', 'club_visitante_id', 'arbitro_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['fecha_programada', 'fecha_reprogramada_1', 'fecha_reprogramada_2', 'fecha_jugada', 'observaciones'], 'safe'],
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
        $query = Fechas::find();

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
            'numero_fecha' => $this->numero_fecha,
            'torneo_id' => $this->torneo_id,
            'fecha_programada' => $this->fecha_programada,
            'fecha_reprogramada_1' => $this->fecha_reprogramada_1,
            'fecha_reprogramada_2' => $this->fecha_reprogramada_2,
            'fecha_jugada' => $this->fecha_jugada,
            'club_local_id' => $this->club_local_id,
            'club_visitante_id' => $this->club_visitante_id,
            'arbitro_id' => $this->arbitro_id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'observaciones', $this->observaciones]);

        return $dataProvider;
    }
}
