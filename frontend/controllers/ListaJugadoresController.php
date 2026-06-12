<?php

namespace frontend\controllers;

use common\models\Categoria;
use common\models\Club;
use common\models\Jugador;
use common\models\ListaJugadores;
use common\models\Partidos;
use frontend\models\ListaJugadoresSearch;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class ListaJugadoresController extends Controller
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
                            'actions' => ['index', 'view', 'lista-partido', 'exportar-excel'],
                            'allow'   => true,
                            'roles'   => ['arbitro', 'directivo', 'miembro_liga', 'admin_liga'],
                        ],
                        [
                            'actions' => ['create', 'update', 'update-remera', 'poblar-lista'],
                            'allow'   => true,
                            'roles'   => ['directivo', 'miembro_liga', 'admin_liga'],
                        ],
                        [
                            'actions' => ['delete'],
                            'allow'   => true,
                            'roles'   => ['admin_liga'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class'   => VerbFilter::class,
                    'actions' => [
                        'delete'        => ['POST'],
                        'update-remera' => ['POST'],
                        'poblar-lista'  => ['POST'],
                    ],
                ],
            ]
        );
    }

    /** Lista general */
    public function actionIndex()
    {
        $searchModel  = new ListaJugadoresSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Vista de lista de partido: tabla pareada 4 columnas (local | remera | visitante | remera).
     * GET /lista-jugadores/lista-partido?partido_id=X
     */
    public function actionListaPartido($partido_id)
    {
        $partido = Partidos::find()
            ->with(['clubLocal', 'clubVisitante', 'fecha'])
            ->where(['id' => $partido_id])
            ->one();

        if (!$partido) {
            throw new NotFoundHttpException('Partido no encontrado.');
        }

        // Jugadores elegibles por categoría y club
        $categoria = Categoria::find()->where(['nombre' => $partido->categoria])->one();
        $catId     = $categoria ? $categoria->id : null;

        $filtroLocal     = ['club_id' => $partido->club_local_id];
        $filtroVisitante = ['club_id' => $partido->club_visitante_id];
        if ($catId) {
            $filtroLocal['categoria_id']     = $catId;
            $filtroVisitante['categoria_id'] = $catId;
        }

        $jugadoresLocales     = Jugador::find()->where($filtroLocal)->orderBy('nombre')->all();
        $jugadoresVisitantes  = Jugador::find()->where($filtroVisitante)->orderBy('nombre')->all();

        // Entradas actuales de la lista
        $locales = ListaJugadores::find()
            ->with('jugador')
            ->where(['partido_id' => $partido_id, 'club_id' => $partido->club_local_id])
            ->orderBy(['remera' => SORT_ASC])
            ->limit(24)
            ->all();

        $visitantes = ListaJugadores::find()
            ->with('jugador')
            ->where(['partido_id' => $partido_id, 'club_id' => $partido->club_visitante_id])
            ->orderBy(['remera' => SORT_ASC])
            ->limit(24)
            ->all();

        $optsRemera = ListaJugadores::optsRemera();

        return $this->render('lista-partido', [
            'partido'             => $partido,
            'locales'             => $locales,
            'visitantes'          => $visitantes,
            'jugadoresLocales'    => $jugadoresLocales,
            'jugadoresVisitantes' => $jugadoresVisitantes,
            'optsRemera'          => $optsRemera,
        ]);
    }

    /**
     * AJAX: actualiza la remera de un entry.
     * POST /lista-jugadores/update-remera  {id, remera}
     */
    public function actionUpdateRemera()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id     = (int) Yii::$app->request->post('id');
        $remera = Yii::$app->request->post('remera');

        $entry = ListaJugadores::findOne($id);
        if (!$entry) {
            return ['success' => false, 'error' => 'Registro no encontrado'];
        }

        $entry->remera = ($remera === '' || $remera === null) ? null : (int) $remera;

        if ($entry->save(false)) {
            return ['success' => true];
        }

        return ['success' => false, 'error' => 'Error al guardar'];
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new ListaJugadores();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                if ($model->partido_id) {
                    return $this->redirect(['lista-partido', 'partido_id' => $model->partido_id]);
                }
                return $this->redirect(['index']);
            }
        } else {
            $model->loadDefaultValues();
            // Pre-seleccionar partido si viene por GET
            $model->partido_id = Yii::$app->request->get('partido_id');
        }

        [$partidos, $jugadoresPorPartido] = $this->getCreateData();

        return $this->render('create', [
            'model'                => $model,
            'partidos'             => $partidos,
            'jugadoresPorPartido'  => $jugadoresPorPartido,
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['lista-partido', 'partido_id' => $model->partido_id]);
        }

        [$partidos, $jugadoresPorPartido] = $this->getCreateData();

        return $this->render('update', [
            'model'               => $model,
            'partidos'            => $partidos,
            'jugadoresPorPartido' => $jugadoresPorPartido,
        ]);
    }

    public function actionDelete($id)
    {
        $entry = $this->findModel($id);
        $partidoId = $entry->partido_id;
        $entry->delete();

        if ($partidoId) {
            return $this->redirect(['lista-partido', 'partido_id' => $partidoId]);
        }
        return $this->redirect(['index']);
    }

    // -------------------------------------------------------------------------

    /**
     * Descarga la lista del partido como archivo Excel (.xls).
     * GET /lista-jugadores/exportar-excel?partido_id=X
     */
    public function actionExportarExcel($partido_id)
    {
        $partido = Partidos::find()
            ->with(['clubLocal', 'clubVisitante', 'fecha'])
            ->where(['id' => $partido_id])
            ->one();

        if (!$partido) {
            throw new NotFoundHttpException('Partido no encontrado.');
        }

        $locales = ListaJugadores::find()
            ->with('jugador')
            ->where(['partido_id' => $partido_id, 'club_id' => $partido->club_local_id])
            ->orderBy(['remera' => SORT_ASC])
            ->all();

        $visitantes = ListaJugadores::find()
            ->with('jugador')
            ->where(['partido_id' => $partido_id, 'club_id' => $partido->club_visitante_id])
            ->orderBy(['remera' => SORT_ASC])
            ->all();

        $localNombre     = $partido->clubLocal     ? $partido->clubLocal->nombre     : 'Local';
        $visitanteNombre = $partido->clubVisitante ? $partido->clubVisitante->nombre : 'Visitante';
        $totalFilas      = max(count($locales), count($visitantes));

        $filename = 'lista_partido_' . $partido_id . '_'
            . preg_replace('/\s+/', '_', $localNombre) . '_vs_'
            . preg_replace('/\s+/', '_', $visitanteNombre) . '.xls';

        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/vnd.ms-excel; charset=utf-8');
        Yii::$app->response->headers->add('Content-Disposition', 'attachment; filename="' . $filename . '"');
        Yii::$app->response->headers->add('Cache-Control', 'max-age=0');

        // Construir HTML que Excel interpreta como planilla
        $html  = '<html xmlns:o="urn:schemas-microsoft-com:office:office"';
        $html .= ' xmlns:x="urn:schemas-microsoft-com:office:excel">';
        $html .= '<head><meta charset="UTF-8">';
        $html .= '<style>
            body  { font-family: Arial, sans-serif; font-size: 11pt; }
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #999; padding: 4px 8px; }
            .titulo   { font-size: 14pt; font-weight: bold; background: #1a1a2e; color: #fff; }
            .subtitulo{ font-size: 10pt; background: #e8e8e8; }
            .th-local { background: #1565C0; color: #fff; font-weight: bold; text-align: center; }
            .th-visit { background: #E65100; color: #fff; font-weight: bold; text-align: center; }
            .th-sub   { background: #90CAF9; font-weight: bold; text-align: center; }
            .th-sub-v { background: #FFCC80; font-weight: bold; text-align: center; }
            .fila-par { background: #F5F5F5; }
            .dt-row   { background: #E0E0E0; font-style: italic; }
        </style></head><body>';

        $html .= '<table>';

        $numeroFecha = $partido->fecha ? 'Fecha #' . $partido->fecha->numero_fecha . ' — ' : '';

        // Título
        $html .= '<tr><td colspan="8" class="titulo">' . $numeroFecha . 'Partido #' . $partido_id . '</td></tr>';
        $html .= '<tr><td colspan="8" class="subtitulo">Categoría: ' . htmlspecialchars($partido->categoria)
              . ' &nbsp;|&nbsp; ' . htmlspecialchars($localNombre)
              . ' vs ' . htmlspecialchars($visitanteNombre) . '</td></tr>';
        $html .= '<tr><td colspan="8" style="padding:2px"></td></tr>';

        // Encabezados de equipo
        $html .= '<tr>'
              . '<th colspan="4" class="th-local">' . htmlspecialchars($localNombre) . '</th>'
              . '<th colspan="4" class="th-visit">' . htmlspecialchars($visitanteNombre) . '</th>'
              . '</tr>';

        // Encabezados de columna
        $html .= '<tr>'
              . '<th class="th-sub">Jugador</th>'
              . '<th class="th-sub">Remera</th>'
              . '<th class="th-sub">DNI</th>'
              . '<th class="th-sub">Firma</th>'
              . '<th class="th-sub-v">Jugador</th>'
              . '<th class="th-sub-v">Remera</th>'
              . '<th class="th-sub-v">DNI</th>'
              . '<th class="th-sub-v">Firma</th>'
              . '</tr>';

        // Filas de datos
        for ($i = 0; $i < $totalFilas; $i++) {
            $l = $locales[$i]    ?? null;
            $v = $visitantes[$i] ?? null;

            $lRemera = $l ? ($l->remera === 0 ? 'DT' : ($l->remera ?? '')) : '';
            $vRemera = $v ? ($v->remera === 0 ? 'DT' : ($v->remera ?? '')) : '';
            $lDni    = ($l && $l->jugador && $lRemera !== 'DT') ? $l->jugador->dni : '';
            $vDni    = ($v && $v->jugador && $vRemera !== 'DT') ? $v->jugador->dni : '';

            $esdt  = ($lRemera === 'DT' || $vRemera === 'DT');
            $clase = $esdt ? 'dt-row' : ($i % 2 === 0 ? '' : 'fila-par');

            $html .= "<tr class=\"{$clase}\">"
                  . '<td>' . htmlspecialchars($l && $l->jugador ? $l->jugador->nombre : '') . '</td>'
                  . '<td style="text-align:center">' . htmlspecialchars((string)$lRemera) . '</td>'
                  . '<td style="text-align:center">' . htmlspecialchars($lDni) . '</td>'
                  . '<td style="min-width:80px">&nbsp;</td>'
                  . '<td>' . htmlspecialchars($v && $v->jugador ? $v->jugador->nombre : '') . '</td>'
                  . '<td style="text-align:center">' . htmlspecialchars((string)$vRemera) . '</td>'
                  . '<td style="text-align:center">' . htmlspecialchars($vDni) . '</td>'
                  . '<td style="min-width:80px">&nbsp;</td>'
                  . '</tr>';
        }

        $html .= '</table></body></html>';

        return $html;
    }

    /**
     * Puebla automáticamente la lista con todos los jugadores de ambos clubes
     * según la categoría del partido. Omite los que ya estén cargados.
     * POST /lista-jugadores/poblar-lista
     */
    public function actionPoblarLista()
    {
        $partido_id = (int) Yii::$app->request->post('partido_id');

        $partido = Partidos::find()
            ->where(['id' => $partido_id])
            ->one();

        if (!$partido) {
            throw new NotFoundHttpException('Partido no encontrado.');
        }

        $categoria = Categoria::find()->where(['nombre' => $partido->categoria])->one();
        $catId     = $categoria ? $categoria->id : null;

        $insertados = 0;

        foreach ([$partido->club_local_id, $partido->club_visitante_id] as $clubId) {
            $q = Jugador::find()->where(['club_id' => $clubId]);
            if ($catId) {
                $q->andWhere(['categoria_id' => $catId]);
            }
            $jugadores = $q->orderBy('nombre')->all();

            foreach ($jugadores as $jugador) {
                $existe = ListaJugadores::find()->where([
                    'partido_id' => $partido_id,
                    'club_id'    => $clubId,
                    'jugador_id' => $jugador->id,
                ])->exists();

                if (!$existe) {
                    $entry              = new ListaJugadores();
                    $entry->tipo_lista  = ListaJugadores::TIPO_PARTIDO;
                    $entry->partido_id  = $partido_id;
                    $entry->club_id     = $clubId;
                    $entry->jugador_id  = $jugador->id;
                    $entry->save(false);
                    $insertados++;
                }
            }
        }

        Yii::$app->session->setFlash('success',
            "Se cargaron {$insertados} jugadores automáticamente.");

        return $this->redirect(['lista-partido', 'partido_id' => $partido_id]);
    }

    /**
     * Datos para el formulario de alta.
     * Devuelve [$partidos, $jugadoresPorPartido].
     * $jugadoresPorPartido = { partido_id: { local: [{id,text}], visitante: [...], local_club_id, visitante_club_id } }
     */
    private function getCreateData(): array
    {
        $todoPartidos = Partidos::find()
            ->with(['clubLocal', 'clubVisitante'])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $partidos = [];
        $jugadoresPorPartido = [];

        foreach ($todoPartidos as $p) {
            $localNombre     = $p->clubLocal     ? $p->clubLocal->nombre     : '?';
            $visitanteNombre = $p->clubVisitante ? $p->clubVisitante->nombre : '?';
            $partidos[$p->id] = "Partido #{$p->id} — {$localNombre} vs {$visitanteNombre}";

            $buildList = function ($clubId) {
                $jugadores = Jugador::find()
                    ->with('categoria')
                    ->where(['club_id' => $clubId])
                    ->orderBy('nombre')
                    ->all();
                return array_map(function ($j) {
                    $cat  = $j->categoria ? ' [' . $j->categoria->nombre . ']' : '';
                    return ['id' => $j->id, 'text' => $j->nombre . ' — DNI ' . $j->dni . $cat];
                }, $jugadores);
            };

            $jugadoresPorPartido[$p->id] = [
                'local_club_id'     => $p->club_local_id,
                'visitante_club_id' => $p->club_visitante_id,
                'local'             => $buildList($p->club_local_id),
                'visitante'         => $buildList($p->club_visitante_id),
            ];
        }

        return [$partidos, Json::encode($jugadoresPorPartido)];
    }

    protected function findModel($id)
    {
        if (($model = ListaJugadores::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('El registro no existe.');
    }
}
