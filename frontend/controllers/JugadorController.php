<?php

namespace frontend\controllers;

use common\models\Jugador;
use frontend\models\JugadorSearch;
use Yii;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class JugadorController extends Controller
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
                            'actions' => ['index', 'view', 'suggest'],
                            'allow'   => true,
                            'roles'   => ['?', '@'],
                        ],
                        [
                            'actions' => ['create', 'update'],
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
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Devuelve sugerencias JSON para autocomplete de nombre o dni.
     * Uso: /jugador/suggest?field=nombre&term=gar
     */
    public function actionSuggest()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $term  = Yii::$app->request->get('term', '');
        $field = Yii::$app->request->get('field', 'nombre');

        if (strlen($term) < 3 || !in_array($field, ['nombre', 'dni'])) {
            return [];
        }

        $clubId = $this->getClubIdScope();
        $query  = Jugador::find()->select($field)->distinct()->limit(10);

        if ($clubId !== null) {
            $query->andWhere(['club_id' => $clubId]);
        }

        $query->andWhere(['like', $field, $term]);

        return $query->column();
    }

    public function actionIndex()
    {
        $searchModel  = new JugadorSearch();
        $clubId       = $this->getClubIdScope();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $clubId);

        return $this->render('index', [
            'searchModel'  => $searchModel,
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
        $model = new Jugador();

        // Directivo solo puede crear jugadores para su propio club
        if ($this->isDirectivoDeClub()) {
            $model->club_id = Yii::$app->user->identity->club_id;
        }

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post())) {
                if ($this->isDirectivoDeClub()) {
                    $model->club_id = Yii::$app->user->identity->club_id;
                }
                if ($model->save()) {
                    $model->uploadFoto();
                    $model->save(false);
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        // Directivo solo puede editar jugadores de su propio club
        if ($this->isDirectivoDeClub()) {
            $userClubId = Yii::$app->user->identity->club_id;
            if ($model->club_id !== $userClubId) {
                throw new ForbiddenHttpException('No tenés permiso para editar jugadores de otro club.');
            }
        }

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post())) {
            if ($this->isDirectivoDeClub()) {
                $model->club_id = Yii::$app->user->identity->club_id;
            }
            if ($model->save()) {
                $model->uploadFoto();
                $model->save(false);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Retorna el club_id del usuario si es directivo de club (no miembro_liga ni admin).
     * Retorna null si puede ver todos los clubes.
     */
    protected function getClubIdScope()
    {
        if ($this->isDirectivoDeClub()) {
            return Yii::$app->user->identity->club_id;
        }
        return null;
    }

    /**
     * True si el usuario tiene rol directivo pero NO es miembro_liga ni admin_liga.
     */
    protected function isDirectivoDeClub()
    {
        $user = Yii::$app->user;
        return $user->can('directivo')
            && !$user->can('miembro_liga')
            && !$user->can('admin_liga');
    }

    protected function findModel($id)
    {
        if (($model = Jugador::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('El jugador no existe.');
    }
}
