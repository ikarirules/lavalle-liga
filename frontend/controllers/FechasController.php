<?php

namespace frontend\controllers;

use common\models\Categoria;
use common\models\Club;
use common\models\Fechas;
use common\models\Partidos;
use common\models\Torneo;
use common\models\User;
use frontend\models\FechasSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * FechasController implements the CRUD actions for Fechas model.
 */
class FechasController extends Controller
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
                            'roles' => ['miembro_liga', 'admin_liga'],
                        ],
                        [
                            'actions' => ['delete'],
                            'allow' => true,
                            'roles' => ['admin_liga'],
                        ],
                        [
                            'actions' => ['arbitros'],
                            'allow' => true,
                            'roles' => ['miembro_liga', 'admin_liga'],
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
     * Lists all Fechas models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new FechasSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Fechas model.
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
     * Creates a new Fechas model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Fechas();
        $categorias = Categoria::find()
            ->where(['activo' => 1])
            ->andWhere(['!=', 'nombre', 'Directivo'])
            ->orderBy('nombre')
            ->all();

        if ($this->request->isPost) {
            $post = $this->request->post();
            $seleccionadas = $post['categorias'] ?? [];

            if ($model->load($post) && $model->validate()) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $model->save(false);

                    foreach ($seleccionadas as $nombreCategoria) {
                        $partido = new Partidos();
                        $partido->fecha_id         = $model->id;
                        $partido->categoria        = $nombreCategoria;
                        $partido->club_local_id    = $model->club_local_id;
                        $partido->club_visitante_id = $model->club_visitante_id;
                        if (!$partido->save()) {
                            throw new \Exception('Error al crear partido para: ' . $nombreCategoria);
                        }
                    }

                    $transaction->commit();
                    return $this->redirect(['view', 'id' => $model->id]);
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    \Yii::$app->session->setFlash('error', $e->getMessage());
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        $torneos = ArrayHelper::map(Torneo::find()->where(['activo' => 1])->all(), 'id', 'nombre');
        $clubes  = ArrayHelper::map(Club::find()->where(['activo' => 1])->orderBy('nombre')->all(), 'id', 'nombre');

        return $this->render('create', [
            'model'      => $model,
            'torneos'    => $torneos,
            'clubes'     => $clubes,
            'categorias' => $categorias,
        ]);
    }

    /**
     * Updates an existing Fechas model.
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

        $torneos = ArrayHelper::map(Torneo::find()->where(['activo' => 1])->all(), 'id', 'nombre');
        $clubes  = ArrayHelper::map(Club::find()->where(['activo' => 1])->orderBy('nombre')->all(), 'id', 'nombre');

        return $this->render('update', [
            'model'   => $model,
            'torneos' => $torneos,
            'clubes'  => $clubes,
        ]);
    }

    /**
     * Deletes an existing Fechas model.
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
     * Finds the Fechas model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Fechas the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    /**
     * Busca árbitros por username (mínimo 3 letras) — responde JSON para Select2.
     * GET /fechas/arbitros?q=rom
     */
    public function actionArbitros()
    {
        \Yii::$app->response->format = Response::FORMAT_JSON;
        $q = trim(\Yii::$app->request->get('q', ''));

        if (mb_strlen($q) < 3) {
            return ['results' => []];
        }

        $users = User::find()
            ->innerJoin('auth_assignment aa', 'aa.user_id = {{%user}}.id')
            ->where(['aa.item_name' => 'arbitro'])
            ->andWhere(['like', '{{%user}}.username', $q])
            ->andWhere(['{{%user}}.status' => User::STATUS_ACTIVE])
            ->limit(20)
            ->all();

        return ['results' => array_map(fn($u) => ['id' => $u->id, 'text' => $u->username], $users)];
    }

    /**
     * Devuelve todos los árbitros activos para precargar el select en update.
     */
    private function getArbitros()
    {
        return ArrayHelper::map(
            User::find()
                ->innerJoin('auth_assignment aa', 'aa.user_id = {{%user}}.id')
                ->where(['aa.item_name' => 'arbitro'])
                ->andWhere(['{{%user}}.status' => User::STATUS_ACTIVE])
                ->all(),
            'id',
            'username'
        );
    }

    protected function findModel($id)
    {
        if (($model = Fechas::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
