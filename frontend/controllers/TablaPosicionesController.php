<?php

namespace frontend\controllers;

use common\models\Categoria;
use common\models\Partidos;
use common\models\Torneo;
use yii\web\Controller;
use yii\filters\AccessControl;

class TablaPosicionesController extends Controller
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'actions' => ['index'],
                            'allow'   => true,
                            'roles'   => ['?', '@'],
                        ],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $torneo = Torneo::find()->where(['activo' => 1])->one();

        $categorias = Categoria::find()->where(['activo' => 1])->all();
        usort($categorias, [$this, 'compararCategorias']);

        $tablas = [];
        foreach ($categorias as $categoria) {
            $tablas[] = [
                'categoria' => $categoria,
                'clubes'    => $this->calcularTabla($categoria->nombre, $torneo),
            ];
        }

        return $this->render('index', [
            'tablas' => $tablas,
            'torneo' => $torneo,
        ]);
    }

    /**
     * "Primera" siempre primero; el resto, orden alfabético.
     */
    protected function compararCategorias(Categoria $a, Categoria $b)
    {
        $aEsPrimera = mb_strtolower($a->nombre) === 'primera';
        $bEsPrimera = mb_strtolower($b->nombre) === 'primera';

        if ($aEsPrimera && !$bEsPrimera) {
            return -1;
        }
        if ($bEsPrimera && !$aEsPrimera) {
            return 1;
        }

        return strcasecmp($a->nombre, $b->nombre);
    }

    /**
     * Calcula la tabla de posiciones de una categoría: PJ, PG, PE, PP, GF, GC, Pts.
     * Los clubes participantes se determinan por los partidos programados en la
     * categoría (jugados o no); los puntos y goles solo se calculan con partidos jugados.
     *
     * @return array[] lista de stats por club, ordenada por puntos (empates: alfabético)
     */
    protected function calcularTabla($categoriaNombre, ?Torneo $torneo)
    {
        $query = Partidos::find()
            ->with(['clubLocal', 'clubVisitante', 'informeArbitral.goles'])
            ->joinWith('fecha')
            ->where(['partidos.categoria' => $categoriaNombre]);

        if ($torneo) {
            $query->andWhere(['fechas.torneo_id' => $torneo->id]);
        }

        $partidos = $query->all();

        $stats = [];

        $asegurarClub = function ($club) use (&$stats) {
            if (!$club) {
                return;
            }
            if (!isset($stats[$club->id])) {
                $stats[$club->id] = [
                    'club' => $club,
                    'pj'   => 0,
                    'pg'   => 0,
                    'pe'   => 0,
                    'pp'   => 0,
                    'gf'   => 0,
                    'gc'   => 0,
                    'pts'  => 0,
                ];
            }
        };

        foreach ($partidos as $partido) {
            $asegurarClub($partido->clubLocal);
            $asegurarClub($partido->clubVisitante);

            if (!$partido->isEstadoJugada()) {
                continue;
            }

            $resultado = $partido->getResultado();
            if ($resultado === null) {
                continue;
            }

            $localId = $partido->club_local_id;
            $visId   = $partido->club_visitante_id;

            $stats[$localId]['pj']++;
            $stats[$visId]['pj']++;
            $stats[$localId]['gf'] += $resultado['local'];
            $stats[$localId]['gc'] += $resultado['visitante'];
            $stats[$visId]['gf']   += $resultado['visitante'];
            $stats[$visId]['gc']   += $resultado['local'];

            if ($resultado['local'] > $resultado['visitante']) {
                $stats[$localId]['pg']++;
                $stats[$localId]['pts'] += 3;
                $stats[$visId]['pp']++;
            } elseif ($resultado['local'] < $resultado['visitante']) {
                $stats[$visId]['pg']++;
                $stats[$visId]['pts'] += 3;
                $stats[$localId]['pp']++;
            } else {
                $stats[$localId]['pe']++;
                $stats[$localId]['pts'] += 1;
                $stats[$visId]['pe']++;
                $stats[$visId]['pts'] += 1;
            }
        }

        $tabla = array_values($stats);

        usort($tabla, function ($a, $b) {
            if ($a['pts'] !== $b['pts']) {
                return $b['pts'] <=> $a['pts'];
            }
            return strcasecmp($a['club']->nombre, $b['club']->nombre);
        });

        return $tabla;
    }
}
