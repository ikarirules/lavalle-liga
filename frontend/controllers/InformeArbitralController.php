<?php

namespace frontend\controllers;

use common\models\Fechas;
use common\models\InformeArbitral;
use common\models\InformeDetalle;
use common\models\InformeGol;
use common\models\Jugador;
use common\models\ListaJugadores;
use common\models\Partidos;
use common\models\TipoInfraccion;
use frontend\models\InformeArbitralSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class InformeArbitralController extends Controller
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
                            'allow' => true,
                            'roles' => ['miembro_liga', 'admin_liga'],
                        ],
                        [
                            'actions' => ['view'],
                            'allow' => true,
                            'roles' => ['arbitro', 'miembro_liga', 'admin_liga'],
                        ],
                        [
                            'actions' => ['create', 'update'],
                            'allow' => true,
                            'roles' => ['arbitro', 'admin_liga'],
                        ],
                        [
                            'actions' => ['jugadores-por-partido'],
                            'allow' => true,
                            'roles' => ['arbitro', 'admin_liga'],
                        ],
                        [
                            'actions' => ['delete'],
                            'allow' => true,
                            'roles' => ['admin_liga'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $searchModel = new InformeArbitralSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCreate()
    {
        $model = new InformeArbitral();
        $isArbitro = \Yii::$app->user->can('arbitro');

        if ($this->request->isPost) {
            $post = $this->request->post();
            if ($model->load($post)) {
                // Forzar árbitro_id si el usuario es árbitro
                if ($isArbitro) {
                    $model->arbitro_id = \Yii::$app->user->id;
                }

                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if (!$model->save()) {
                        throw new \Exception('Error en cabecera del informe');
                    }

                    // Goles
                    foreach ($post['InformeGol'] ?? [] as $data) {
                        if (empty($data['jugador_id']) || empty($data['club_id'])) continue;
                        $gol = new InformeGol();
                        $gol->informe_id = $model->id;
                        $gol->jugador_id = (int)$data['jugador_id'];
                        $gol->club_id    = (int)$data['club_id'];
                        $gol->cantidad   = max(1, (int)($data['cantidad'] ?? 1));
                        if (!$gol->save()) throw new \Exception('Error guardando gol');
                    }

                    // Infracciones
                    $partido = Partidos::findOne($model->partido_id);
                    $fecha   = $partido ? Fechas::findOne($partido->fecha_id) : null;

                    foreach ($post['InformeDetalle'] ?? [] as $data) {
                        if (empty($data['tipo_infraccion_id']) || empty($data['club_id'])) continue;
                        $detalle = new InformeDetalle();
                        $detalle->informe_id         = $model->id;
                        $detalle->club_id            = (int)$data['club_id'];
                        $detalle->jugador_id         = !empty($data['jugador_id']) ? (int)$data['jugador_id'] : null;
                        $detalle->tipo_infraccion_id = (int)$data['tipo_infraccion_id'];
                        $detalle->minuto             = !empty($data['minuto']) ? (int)$data['minuto'] : null;
                        if (!$detalle->save()) throw new \Exception('Error guardando infracción');

                        // Aplicar suspensión al jugador si corresponde
                        if ($detalle->jugador_id && $fecha) {
                            $tipo = TipoInfraccion::findOne($detalle->tipo_infraccion_id);
                            if ($tipo && $tipo->sancion_fechas_min > 0) {
                                $jugador = Jugador::findOne($detalle->jugador_id);
                                if ($jugador) {
                                    $jugador->numero_fecha_suspension = $fecha->numero_fecha;
                                    $jugador->cant_fechas_suspension  = $tipo->sancion_fechas_min;
                                    $jugador->save(false);
                                }
                            }
                        }
                    }

                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $model->id]);

                } catch (\Exception $e) {
                    $transaction->rollBack();
                    \Yii::$app->session->addFlash('error', $e->getMessage());
                }
            }
        } else {
            $model->loadDefaultValues();
            if ($isArbitro) {
                $model->arbitro_id = \Yii::$app->user->id;
            }
        }

        // Fechas de los últimos 30 días y próximos 15, ordenadas por proximidad a hoy
        $fechas = Fechas::find()
            ->where(['between', 'fecha_programada',
                date('Y-m-d', strtotime('-30 days')),
                date('Y-m-d', strtotime('+15 days')),
            ])
            ->orderBy(new \yii\db\Expression('ABS(DATEDIFF(fecha_programada, CURDATE()))'))
            ->all();

        // Partidos agrupados por fecha (optgroups en el dropdown)
        $partidosOptions = [];
        foreach ($fechas as $f) {
            $partidos = Partidos::find()
                ->with(['clubLocal', 'clubVisitante'])
                ->where(['fecha_id' => $f->id])
                ->all();
            if (empty($partidos)) continue;
            $grupoLabel = 'Fecha ' . $f->numero_fecha . ' — ' . $f->fecha_programada;
            foreach ($partidos as $p) {
                $local     = $p->clubLocal     ? $p->clubLocal->nombre     : '?';
                $visitante = $p->clubVisitante ? $p->clubVisitante->nombre : '?';
                $partidosOptions[$grupoLabel][$p->id] = $local . ' vs ' . $visitante . ' (' . $p->categoria . ')';
            }
        }

        $tiposOptions = ArrayHelper::map(TipoInfraccion::find()->all(), 'id', 'nombre');

        $arbitroIds = \Yii::$app->db->createCommand(
            "SELECT user_id FROM auth_assignment WHERE item_name = 'arbitro'"
        )->queryColumn();

        $arbitrosOptions = $arbitroIds
            ? ArrayHelper::map(
                \common\models\User::find()
                    ->where(['id' => $arbitroIds, 'status' => \common\models\User::STATUS_ACTIVE])
                    ->orderBy('username')
                    ->all(),
                'id',
                'username'
            )
            : [];

        return $this->render('create', [
            'model'           => $model,
            'partidosOptions' => $partidosOptions,
            'isArbitro'       => $isArbitro,
            'tiposOptions'    => $tiposOptions,
            'arbitrosOptions' => $arbitrosOptions,
        ]);
    }

    /**
     * AJAX: devuelve los jugadores de la lista de un partido agrupados por equipo.
     */
    public function actionJugadoresPorPartido($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $partido = Partidos::findOne((int)$id);
        if (!$partido) return [];

        $lista = ListaJugadores::find()
            ->with('jugador')
            ->where(['partido_id' => (int)$id])
            ->orderBy(['remera' => SORT_ASC])
            ->all();

        $result = [
            'local' => [
                'club_id'   => $partido->club_local_id,
                'nombre'    => $partido->clubLocal ? $partido->clubLocal->nombre : '?',
                'jugadores' => [],
            ],
            'visitante' => [
                'club_id'   => $partido->club_visitante_id,
                'nombre'    => $partido->clubVisitante ? $partido->clubVisitante->nombre : '?',
                'jugadores' => [],
            ],
        ];

        foreach ($lista as $item) {
            if (!$item->jugador) continue;
            $jData = [
                'id'     => $item->jugador_id,
                'nombre' => $item->jugador->nombre,
                'remera' => $item->remera,
            ];
            if ($item->club_id == $partido->club_local_id) {
                $result['local']['jugadores'][] = $jData;
            } else {
                $result['visitante']['jugadores'][] = $jData;
            }
        }

        return $result;
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = InformeArbitral::findOne(['id' => $id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
