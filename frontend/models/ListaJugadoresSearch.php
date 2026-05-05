<?php

namespace frontend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ListaJugadores;

class ListaJugadoresSearch extends ListaJugadores
{
    public function rules()
    {
        return [
            [['id', 'partido_id', 'club_id', 'jugador_id', 'remera',
              'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['tipo_lista'], 'safe'],
        ];
    }

    public function scenarios()
    {
        return Model::scenarios();
    }

    public function search($params, $formName = null)
    {
        $query = ListaJugadores::find()->with(['club', 'jugador', 'partido']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'  => ['defaultOrder' => ['id' => SORT_DESC]],
        ]);

        $this->load($params, $formName);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id'               => $this->id,
            'tipo_lista'       => $this->tipo_lista,
            'partido_id'       => $this->partido_id,
            'club_id'          => $this->club_id,
            'jugador_id' => $this->jugador_id,
            'remera'     => $this->remera,
        ]);

        return $dataProvider;
    }
}
