<?php

namespace frontend\controllers;

use common\models\Categoria;
use common\models\Club;
use common\models\Jugador;
use common\models\ListaJugadores;
use common\models\Partidos;
use frontend\models\ListaJugadoresSearch;
use Yii;
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
                            'actions' => ['index', 'view', 'lista-partido', 'exportar-excel', 'plantel'],
                            'allow'   => true,
                            'roles'   => ['arbitro', 'directivo', 'miembro_liga', 'admin_liga'],
                        ],
                        [
                            'actions' => ['create', 'update', 'update-remera', 'poblar-lista', 'jugadores-partido', 'plantel-agregar'],
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
                        'delete'          => ['POST'],
                        'update-remera'   => ['POST'],
                        'poblar-lista'    => ['POST'],
                        'plantel-agregar' => ['POST'],
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

        return $this->render('create', [
            'model'    => $model,
            'partidos' => $this->getPartidosList(),
        ]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['lista-partido', 'partido_id' => $model->partido_id]);
        }

        return $this->render('update', [
            'model'    => $model,
            'partidos' => $this->getPartidosList(),
        ]);
    }

    /**
     * AJAX: jugadores elegibles de un partido/lado (local o visitante).
     * GET /lista-jugadores/jugadores-partido?partido_id=X&lado=local|visitante
     */
    public function actionJugadoresPartido($partido_id, $lado)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $partido = Partidos::find()->where(['id' => $partido_id])->one();
        if (!$partido) {
            throw new NotFoundHttpException('Partido no encontrado.');
        }

        $clubId = $lado === 'visitante' ? $partido->club_visitante_id : $partido->club_local_id;

        return [
            'club_id'   => $clubId,
            'jugadores' => $this->buildJugadoresList($clubId),
        ];
    }

    public function actionDelete($id)
    {
        $entry     = $this->findModel($id);
        $partidoId = $entry->partido_id;
        $clubId    = $entry->club_id;
        $esBuenaFe = $entry->tipo_lista === ListaJugadores::TIPO_BUENA_FE;
        $entry->delete();

        if ($esBuenaFe) {
            return $this->redirect(['plantel', 'club_id' => $clubId]);
        }
        if ($partidoId) {
            return $this->redirect(['lista-partido', 'partido_id' => $partidoId]);
        }
        return $this->redirect(['index']);
    }

    /**
     * Plantel maestro ("Buena Fe") de un club: jugadores que se cargan una sola vez
     * por temporada y se heredan automáticamente en cada partido (ver actionPoblarLista).
     * GET /lista-jugadores/plantel?club_id=X
     */
    public function actionPlantel($club_id)
    {
        $club = Club::findOne($club_id);
        if (!$club) {
            throw new NotFoundHttpException('Club no encontrado.');
        }

        $plantel = ListaJugadores::find()
            ->joinWith(['jugador' => function ($q) { $q->with('categoria'); }])
            ->where(['lista_jugadores.tipo_lista' => ListaJugadores::TIPO_BUENA_FE, 'lista_jugadores.club_id' => $club_id])
            ->orderBy(['jugador.nombre' => SORT_ASC])
            ->all();

        $idsEnPlantel = array_map(fn($e) => $e->jugador_id, $plantel);

        $disponibles = Jugador::find()
            ->with('categoria')
            ->where([
                'or',
                ['and', ['club_id' => $club_id], ['club_pase_id' => null]],
                ['club_pase_id' => $club_id],
            ])
            ->andWhere(['not in', 'id', $idsEnPlantel ?: [0]])
            ->orderBy('nombre')
            ->all();

        return $this->render('plantel', [
            'club'        => $club,
            'plantel'     => $plantel,
            'disponibles' => $disponibles,
            'optsRemera'  => ListaJugadores::optsRemera(),
        ]);
    }

    /**
     * Agrega un jugador al plantel maestro (Buena Fe) de un club.
     * POST /lista-jugadores/plantel-agregar  {club_id, jugador_id}
     */
    public function actionPlantelAgregar()
    {
        $clubId    = (int) Yii::$app->request->post('club_id');
        $jugadorId = (int) Yii::$app->request->post('jugador_id');

        if ($clubId && $jugadorId) {
            $existe = ListaJugadores::find()->where([
                'tipo_lista' => ListaJugadores::TIPO_BUENA_FE,
                'club_id'    => $clubId,
                'jugador_id' => $jugadorId,
            ])->exists();

            if (!$existe) {
                $entry              = new ListaJugadores();
                $entry->tipo_lista  = ListaJugadores::TIPO_BUENA_FE;
                $entry->club_id     = $clubId;
                $entry->jugador_id  = $jugadorId;
                $entry->save(false);
            } else {
                Yii::$app->session->setFlash('warning', 'Ese jugador ya está en el plantel.');
            }
        }

        return $this->redirect(['plantel', 'club_id' => $clubId]);
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

        // Directivos/DT = los que se cargaron al crear el partido (dt1_local/dt2_local/dt1_visitante/dt2_visitante)
        $directivosLocalNombres     = implode(', ', array_filter([$partido->dt1_local, $partido->dt2_local]));
        $directivosVisitanteNombres = implode(', ', array_filter([$partido->dt1_visitante, $partido->dt2_visitante]));

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
        // Configuración de página de Excel: horizontal, ajustada a 1 hoja A4 de ancho
        $html .= '<!--[if gte mso 9]><xml>
            <x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>
            <x:Name>Lista</x:Name>
            <x:WorksheetOptions>
                <x:PageSetup>
                    <x:Layout x:Orientation="Landscape"/>
                    <x:PageMargins x:Bottom="0.5" x:Left="0.3" x:Right="0.3" x:Top="0.5"/>
                </x:PageSetup>
                <x:Print>
                    <x:FitWidth>1</x:FitWidth>
                    <x:FitHeight>0</x:FitHeight>
                    <x:ValidPrinterInfo/>
                </x:Print>
                <x:DoNotDisplayGridlines/>
            </x:WorksheetOptions>
            </x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook>
        </xml><![endif]-->';
        $html .= '<style>
            body  { font-family: Arial, sans-serif; font-size: 11pt; }
            table { border-collapse: collapse; width: 100%; table-layout: fixed; }
            th, td { border: 1px solid #999; padding: 4px 8px; overflow: hidden; }
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
        $html .= '<colgroup>'
              . '<col style="width:140pt">'
              . '<col style="width:26pt">'
              . '<col style="width:60pt">'
              . '<col style="width:60pt">'
              . '<col style="width:140pt">'
              . '<col style="width:26pt">'
              . '<col style="width:60pt">'
              . '<col style="width:60pt">'
              . '</colgroup>';

        $numeroFecha = $partido->fecha ? 'Fecha #' . $partido->fecha->numero_fecha . ' — ' : '';

        // Título
        $html .= '<tr><td colspan="8" class="titulo">' . $numeroFecha . 'Partido #' . $partido_id . '</td></tr>';
        $html .= '<tr><td colspan="8" class="subtitulo">Categoría: ' . htmlspecialchars($partido->categoria)
              . ' &nbsp;|&nbsp; ' . htmlspecialchars($localNombre)
              . ' vs ' . htmlspecialchars($visitanteNombre) . '</td></tr>';
        $html .= '<tr>'
              . '<td colspan="4" class="subtitulo">Directivo/s: ' . htmlspecialchars($directivosLocalNombres ?: '—') . '</td>'
              . '<td colspan="4" class="subtitulo">Directivo/s: ' . htmlspecialchars($directivosVisitanteNombres ?: '—') . '</td>'
              . '</tr>';
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
                  . '<td style="white-space:normal;word-wrap:break-word;font-size:9pt">' . htmlspecialchars($l && $l->jugador ? $l->jugador->nombre : '') . '</td>'
                  . '<td style="text-align:center;white-space:nowrap">' . htmlspecialchars((string)$lRemera) . '</td>'
                  . '<td style="text-align:center;white-space:nowrap;font-size:9pt">' . htmlspecialchars($lDni) . '</td>'
                  . '<td>&nbsp;</td>'
                  . '<td style="white-space:normal;word-wrap:break-word;font-size:9pt">' . htmlspecialchars($v && $v->jugador ? $v->jugador->nombre : '') . '</td>'
                  . '<td style="text-align:center;white-space:nowrap">' . htmlspecialchars((string)$vRemera) . '</td>'
                  . '<td style="text-align:center;white-space:nowrap;font-size:9pt">' . htmlspecialchars($vDni) . '</td>'
                  . '<td>&nbsp;</td>'
                  . '</tr>';
        }

        $html .= '</table></body></html>';

        return $html;
    }

    /**
     * Puebla la lista de ambos clubes del partido (ver ListaJugadores::poblarPartido).
     * Se ejecuta automáticamente al crear el partido; este botón permite recargarla.
     * Omite los que ya estén cargados.
     *
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

        [$insertados, $omitidos] = ListaJugadores::poblarPartido($partido);

        $mensaje = "Se cargaron {$insertados} jugadores automáticamente.";
        if ($omitidos > 0) {
            $mensaje .= " Se omitieron {$omitidos} por estar suspendidos.";
        }
        Yii::$app->session->setFlash('success', $mensaje);

        return $this->redirect(['lista-partido', 'partido_id' => $partido_id]);
    }

    /** Lista de partidos para el dropdown del formulario: [id => label]. */
    private function getPartidosList(): array
    {
        $todoPartidos = Partidos::find()
            ->with(['clubLocal', 'clubVisitante'])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        $partidos = [];
        foreach ($todoPartidos as $p) {
            $localNombre     = $p->clubLocal     ? $p->clubLocal->nombre     : '?';
            $visitanteNombre = $p->clubVisitante ? $p->clubVisitante->nombre : '?';
            $partidos[$p->id] = "Partido #{$p->id} — {$localNombre} vs {$visitanteNombre}";
        }

        return $partidos;
    }

    /** Jugadores elegibles de un club (propios sin pase a otro club, o con pase hacia este). */
    private function buildJugadoresList(?int $clubId): array
    {
        if (!$clubId) {
            return [];
        }

        $jugadores = Jugador::find()
            ->with('categoria')
            ->where([
                'or',
                ['and', ['club_id' => $clubId], ['club_pase_id' => null]],
                ['club_pase_id' => $clubId],
            ])
            ->orderBy('nombre')
            ->all();

        // Un sancionado/suspendido no debería poder anotarse en la lista de un partido
        $jugadores = array_values(array_filter($jugadores, fn($j) => !$j->suspendido));

        return array_map(function ($j) {
            $cat = $j->categoria ? ' [' . $j->categoria->nombre . ']' : '';
            return ['id' => $j->id, 'text' => $j->nombre . ' — DNI ' . $j->dni . $cat];
        }, $jugadores);
    }

    protected function findModel($id)
    {
        if (($model = ListaJugadores::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('El registro no existe.');
    }
}
