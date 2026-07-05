<?php

namespace frontend\models;

use common\models\Multa;
use yii\data\ActiveDataProvider;

class MultaSearch extends Multa
{
    public $jugador_nombre;
    public $club_id;

    public function rules()
    {
        return [
            [['jugador_id', 'pagado', 'club_id'], 'integer'],
            [['jugador_nombre'], 'safe'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = Multa::find()
            ->joinWith(['jugador.club', 'informeDetalle.tipoInfraccion']);

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->jugador_id !== null && $this->jugador_id !== '') {
            $query->andWhere(['multa.jugador_id' => $this->jugador_id]);
        }

        if ($this->pagado !== null && $this->pagado !== '') {
            $query->andWhere(['multa.pagado' => $this->pagado]);
        }

        if ($this->club_id !== null && $this->club_id !== '') {
            $query->andWhere(['jugador.club_id' => $this->club_id]);
        }

        if ($this->jugador_nombre) {
            $query->andWhere(['like', 'jugador.nombre', $this->jugador_nombre]);
        }

        return $dataProvider;
    }
}
