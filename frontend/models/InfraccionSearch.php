<?php

namespace frontend\models;

use common\models\InformeDetalle;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * Lista las infracciones a jugadores que generan multa y/o sanción de fechas
 * (informe_detalle + tipo_infraccion), con la multa asociada si existe.
 */
class InfraccionSearch extends Model
{
    public $jugador_nombre;
    public $club_id;
    public $pagado;

    public function rules()
    {
        return [
            [['club_id', 'pagado'], 'integer'],
            [['jugador_nombre'], 'safe'],
        ];
    }

    public function search(array $params): ActiveDataProvider
    {
        $query = InformeDetalle::find()
            ->joinWith(['jugador.club', 'tipoInfraccion', 'multa'])
            ->andWhere(['is not', 'informe_detalle.jugador_id', null])
            ->andWhere(['or',
                ['tipo_infraccion.genera_multa' => 1],
                ['>', 'tipo_infraccion.sancion_fechas_min', 0],
            ]);

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'sort'       => ['defaultOrder' => ['id' => SORT_DESC]],
            'pagination' => ['pageSize' => 30],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->club_id) {
            $query->andWhere(['jugador.club_id' => $this->club_id]);
        }

        if ($this->pagado !== null && $this->pagado !== '') {
            $query->andWhere(['multa.pagado' => $this->pagado]);
        }

        if ($this->jugador_nombre) {
            $query->andWhere(['like', 'jugador.nombre', $this->jugador_nombre]);
        }

        return $dataProvider;
    }
}
