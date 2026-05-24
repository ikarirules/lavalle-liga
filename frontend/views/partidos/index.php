<?php

use common\models\Partidos;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\PartidosSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Partidos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partidos-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Partidos', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager' => [
            'class' => \yii\bootstrap5\LinkPager::class,
            'firstPageLabel' => false,
            'lastPageLabel'  => false,
            'prevPageLabel'  => '‹',
            'nextPageLabel'  => '›',
            'maxButtonCount' => 7,
        ],
        'columns' => [
            
            [
                'attribute' => 'fecha_id',
                'label' => 'N° Fecha',
                'value' => fn($model) => $model->fecha ? $model->fecha->numero_fecha : $model->fecha_id,
            ],
            'categoria',
            [
                'attribute' => 'club_local_id',
                'label' => 'Club Local',
                'value' => fn($model) => $model->clubLocal ? $model->clubLocal->nombre : $model->club_local_id,
            ],
            [
                'attribute' => 'club_visitante_id',
                'label' => 'Club Visitante',
                'value' => fn($model) => $model->clubVisitante ? $model->clubVisitante->nombre : $model->club_visitante_id,
            ],
            //'cancha',
            //'estado',
            //'goles_local',
            //'goles_visitante',
            //'created_by',
            //'updated_by',
            //'created_at',
            //'updated_at',
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Partidos $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'buttons' => [
                    'update' => function ($url) {
                        return Html::a('Editar', $url, ['class' => 'btn btn-primary btn-sm']);
                    },
                    'view' => function ($url) {
                        return Html::a('Ver', $url, ['class' => 'btn btn-secondary btn-sm']);
                    },
                    'delete' => function ($url) {
                        return Html::a('Eliminar', $url, [
                            'class' => 'btn btn-danger btn-sm',
                            'data' => ['confirm' => '¿Eliminar este partido?', 'method' => 'post'],
                        ]);
                    },
                ],
                'contentOptions' => ['style' => 'white-space:nowrap'],
            ],
        ],
    ]); ?>


</div>
