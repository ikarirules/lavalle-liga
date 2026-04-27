<?php

namespace frontend\controllers;

use common\models\Categoria;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

class CategoriaController extends Controller
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
                            'roles'   => ['jugador', 'arbitro', 'directivo', 'miembro_liga', 'admin_liga'],
                        ],
                        [
                            'actions' => ['update'],
                            'allow'   => true,
                            'roles'   => ['miembro_liga', 'admin_liga'],
                        ],
                    ],
                ],
                'verbs' => [
                    'class'   => VerbFilter::class,
                    'actions' => ['toggle-bf' => ['POST']],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $categorias = Categoria::find()->orderBy('nombre')->all();

        return $this->render('index', ['categorias' => $categorias]);
    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->request->isPost && $model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Categoría actualizada.');
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Toggle rápido de permite_bf desde el index (POST).
     */
    public function actionToggleBf($id)
    {
        $model = $this->findModel($id);
        $model->permite_bf = $model->permite_bf ? 0 : 1;
        $model->save(false);

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Categoria::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('La categoría no existe.');
    }
}
