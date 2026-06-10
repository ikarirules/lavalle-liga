<?php

use common\models\Fechas;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\FechasSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Fechas';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fechas-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Crear Fechas', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'numero_fecha',
            [
                'attribute' => 'torneo_id',
                'label'     => 'Torneo',
                'value'     => fn($model) => $model->torneo ? $model->torneo->nombre : '-',
            ],
            'fecha_programada',
            [
                'attribute' => 'club_local_id',
                'label'     => 'Club Local',
                'value'     => fn($model) => $model->clubLocal ? $model->clubLocal->nombre : '-',
            ],
            [
                'attribute' => 'club_visitante_id',
                'label'     => 'Club Visitante',
                'value'     => fn($model) => $model->clubVisitante ? $model->clubVisitante->nombre : '-',
            ],
            'fecha_reprogramada_1',
            //'fecha_reprogramada_2',
            //'fecha_jugada',
            //'club_local_id',
            //'club_visitante_id',
            //'arbitro_id',
            //'observaciones:ntext',
            //'created_by',
            //'updated_by',
            //'created_at',
            //'updated_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Fechas $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
