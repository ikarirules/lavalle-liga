<?php

namespace frontend\controllers;

use common\models\Club;
use common\models\Jugador;
use common\models\Multa;
use frontend\models\MultaSearch;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class MultaController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow'   => true,
                        'roles'   => ['directivo', 'miembro_liga', 'admin_liga'],
                        'actions' => ['index', 'view', 'marcar-pagada', 'desmarcar-pagada'],
                    ],
                ],
            ],
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'marcar-pagada'    => ['POST'],
                    'desmarcar-pagada' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $searchModel  = new MultaSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $clubs    = Club::find()->orderBy('nombre')->all();
        $jugadores = Jugador::find()->orderBy('nombre')->all();

        return $this->render('index', [
            'searchModel'  => $searchModel,
            'dataProvider' => $dataProvider,
            'clubs'        => $clubs,
            'jugadores'    => $jugadores,
        ]);
    }

    public function actionView(int $id)
    {
        $multa = $this->findModel($id);

        $reincidencia = Multa::find()
            ->where(['jugador_id' => $multa->jugador_id])
            ->andWhere(['<>', 'id', $multa->id])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        return $this->render('view', [
            'multa'       => $multa,
            'reincidencia' => $reincidencia,
        ]);
    }

    public function actionMarcarPagada(int $id)
    {
        $multa = $this->findModel($id);
        $obs   = Yii::$app->request->post('observaciones', '');

        $multa->pagado        = 1;
        $multa->fecha_pago    = date('Y-m-d');
        $multa->observaciones = $obs ?: $multa->observaciones;

        if ($multa->save(false)) {
            // Levantar suspensión si sigue activa y fue generada por esta infracción
            $jugador = $multa->jugador;
            if ($jugador && $jugador->suspendido) {
                $jugador->numero_fecha_suspension = null;
                $jugador->cant_fechas_suspension  = 0;
                $jugador->save(false);
            }
            Yii::$app->session->setFlash('success', 'Multa marcada como pagada. Suspensión levantada.');
        } else {
            Yii::$app->session->setFlash('error', 'No se pudo guardar.');
        }

        return $this->redirect(['index']);
    }

    public function actionDesmarcarPagada(int $id)
    {
        $multa             = $this->findModel($id);
        $multa->pagado     = 0;
        $multa->fecha_pago = null;
        $multa->save(false);

        Yii::$app->session->setFlash('warning', 'Multa marcada como pendiente de pago.');
        return $this->redirect(['index']);
    }

    protected function findModel(int $id): Multa
    {
        $model = Multa::findOne($id);
        if (!$model) {
            throw new NotFoundHttpException('Multa no encontrada.');
        }
        return $model;
    }
}
