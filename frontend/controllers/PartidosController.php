<?php

namespace frontend\controllers;

use common\models\Categoria;
use common\models\Fechas;
use common\models\Jugador;
use common\models\Partidos;
use common\models\User;
use frontend\models\PartidosSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * PartidosController implements the CRUD actions for Partidos model.
 */
class PartidosController extends Controller
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
                            'actions' => ['index', 'view'],
                            'allow' => true,
                            'roles' => ['jugador', 'arbitro', 'directivo', 'miembro_liga', 'admin_liga'],
                        ],
                        [
                            'actions' => ['create', 'update'],
                            'allow' => true,
                            'roles' => ['arbitro', 'directivo', 'miembro_liga', 'admin_liga'],
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

    /**
     * Lists all Partidos models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PartidosSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Partidos model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Partidos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Partidos();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
            if (Yii::$app->user->can('arbitro')) {
                $model->arbitro = Yii::$app->user->identity->username;
            }
        }

        [$fechasOptions, $fechasData, $isArbitro, $arbitros, $categorias, $directivosPorClub] = $this->getFormData();

        return $this->render('create', [
            'model'              => $model,
            'fechasOptions'      => $fechasOptions,
            'fechasData'         => $fechasData,
            'isArbitro'          => $isArbitro,
            'arbitros'           => $arbitros,
            'categorias'         => $categorias,
            'directivosPorClub'  => $directivosPorClub,
        ]);
    }

    /**
     * Updates an existing Partidos model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        [$fechasOptions, $fechasData, $isArbitro, $arbitros, $categorias, $directivosPorClub] = $this->getFormData();

        return $this->render('update', [
            'model'              => $model,
            'fechasOptions'      => $fechasOptions,
            'fechasData'         => $fechasData,
            'isArbitro'          => $isArbitro,
            'arbitros'           => $arbitros,
            'categorias'         => $categorias,
            'directivosPorClub'  => $directivosPorClub,
        ]);
    }

    /**
     * Deletes an existing Partidos model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Partidos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Partidos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    private function getFormData(): array
    {
        $categorias = ArrayHelper::map(
            Categoria::find()->where(['activo' => 1])->orderBy('nombre')->all(),
            'nombre',
            'nombre'
        );

        $isArbitro = Yii::$app->user->can('arbitro');

        $fechasList = Fechas::find()
            ->with(['clubLocal', 'clubVisitante'])
            ->orderBy(['fecha_programada' => SORT_ASC])
            ->all();

        $fechasOptions = [];
        $fechasData    = [];
        foreach ($fechasList as $fecha) {
            $local      = $fecha->clubLocal     ? $fecha->clubLocal->nombre     : '?';
            $visitante  = $fecha->clubVisitante ? $fecha->clubVisitante->nombre : '?';
            $fechaLabel = 'Fecha #' . $fecha->numero_fecha . ' — '
                . Yii::$app->formatter->asDate($fecha->fecha_programada, 'dd/MM/yyyy')
                . ' | ' . $local . ' vs ' . $visitante;
            $fechasOptions[$fecha->id] = $fechaLabel;
            $fechasData[$fecha->id] = [
                'fecha_programada'    => $fecha->fecha_programada
                    ? Yii::$app->formatter->asDatetime($fecha->fecha_programada, 'dd/MM/yyyy HH:mm')
                    : '',
                'club_local_id'       => $fecha->club_local_id,
                'club_local_nombre'   => $fecha->clubLocal ? $fecha->clubLocal->nombre : '',
                'club_visitante_id'   => $fecha->club_visitante_id,
                'club_visitante_nombre' => $fecha->clubVisitante ? $fecha->clubVisitante->nombre : '',
            ];
        }

        $arbitros = [];
        if (!$isArbitro) {
            $arbitros = ArrayHelper::map(
                User::find()
                    ->innerJoin('auth_assignment aa', 'aa.user_id = {{%user}}.id')
                    ->where(['aa.item_name' => 'arbitro'])
                    ->andWhere(['{{%user}}.status' => User::STATUS_ACTIVE])
                    ->orderBy('{{%user}}.username')
                    ->all(),
                'username',
                'username'
            );
        }

        // Directivos = jugadores con categoría "Directivo", agrupados por club
        $directivosPorClub = [];
        $allDirectivos = Jugador::find()
            ->joinWith('categoria', true)
            ->where(['categoria.nombre' => 'Directivo'])
            ->orderBy('jugador.nombre')
            ->all();
        foreach ($allDirectivos as $j) {
            $directivosPorClub[$j->club_id][$j->nombre] = $j->nombre;
        }

        return [$fechasOptions, $fechasData, $isArbitro, $arbitros, $categorias, $directivosPorClub];
    }

    protected function findModel($id)
    {
        if (($model = Partidos::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
