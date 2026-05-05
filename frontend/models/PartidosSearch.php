<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Partidos;

/**
 * PartidosSearch represents the model behind the search form of `common\models\Partidos`.
 */
class PartidosSearch extends Partidos
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'fecha_id', 'club_local_id', 'club_visitante_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['categoria', 'cancha', 'estado', 'arbitro', 'asistente1', 'asistente2', 'asistente3'], 'safe'],
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
        $query = Partidos::find();

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
            'fecha_id' => $this->fecha_id,
            'club_local_id' => $this->club_local_id,
            'club_visitante_id' => $this->club_visitante_id,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'categoria', $this->categoria])
            ->andFilterWhere(['like', 'cancha', $this->cancha])
            ->andFilterWhere(['like', 'estado', $this->estado])
            ->andFilterWhere(['like', 'arbitro', $this->arbitro])
            ->andFilterWhere(['like', 'asistente1', $this->asistente1])
            ->andFilterWhere(['like', 'asistente2', $this->asistente2])
            ->andFilterWhere(['like', 'asistente3', $this->asistente3]);

        return $dataProvider;
    }
}
