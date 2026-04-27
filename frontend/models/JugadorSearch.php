<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Jugador;

/**
 * JugadorSearch represents the model behind the search form of `common\models\Jugador`.
 */
class JugadorSearch extends Jugador
{
    public function rules()
    {
        return [
            [['id', 'club_id', 'categoria_id', 'numero_fecha_suspension', 'cant_fechas_suspension'], 'integer'],
            [['nombre', 'dni', 'fecha_nacimiento'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * @param array $params
     * @param int|null $clubId Si se pasa, filtra solo jugadores de ese club (para directivos).
     */
    public function search($params, $clubId = null)
    {
        $query = Jugador::find()->with(['club', 'categoria']);

        if ($clubId !== null) {
            $query->andWhere(['club_id' => $clubId]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['nombre' => SORT_ASC]],
        ]);

        $this->load($params, 'JugadorSearch');

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id'                      => $this->id,
            'club_id'                 => $this->club_id,
            'categoria_id'            => $this->categoria_id,
            'numero_fecha_suspension' => $this->numero_fecha_suspension,
            'cant_fechas_suspension'  => $this->cant_fechas_suspension,
        ]);

        $query->andFilterWhere(['like', 'nombre', $this->nombre])
            ->andFilterWhere(['like', 'dni', $this->dni]);

        return $dataProvider;
    }
}
