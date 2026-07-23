<?php

use common\models\Club;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/** @var frontend\models\JugadorSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Jugadores';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jugador-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->can('editarJugadores')): ?>
    <p>
        <?= Html::a('Nuevo Jugador', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php endif; ?>

    <?php Pjax::begin(['id' => 'jugador-pjax', 'timeout' => 5000]); ?>

    <?php
    $columns = ['nombre'];

    if (!Yii::$app->user->isGuest) {
        $columns[] = 'dni';
        $columns[] = [
            'attribute' => 'fecha_nacimiento',
            'label'     => 'F. Nacimiento',
            'format'    => 'date',
        ];
    }

    $columns[] = [
        'attribute' => 'categoria_id',
        'label'     => 'Categoría',
        'value'     => fn($model) => $model->categoria ? $model->categoria->nombre : '-',
        'filter'    => \common\models\Categoria::lista(),
    ];
    $columns[] = [
        'attribute' => 'club_id',
        'label'     => 'Club',
        'value'     => fn($model) => $model->club ? $model->club->nombre : '-',
        'filter'    => ArrayHelper::map(
            Club::find()->orderBy('nombre')->all(),
            'id', 'nombre'
        ),
    ];
    $columns[] = [
        'attribute' => 'club_pase_id',
        'label'     => 'Pase',
        'value'     => fn($model) => $model->clubPase ? $model->clubPase->nombre : '-',
        'filter'    => ArrayHelper::map(
            Club::find()->orderBy('nombre')->all(),
            'id', 'nombre'
        ),
    ];
    $columns[] = [
        'label'  => 'Estado',
        'format' => 'raw',
        'value'  => function ($model) {
            if ($model->suspendido) {
                return '<span class="badge bg-danger">Suspendido</span>';
            }
            return '<span class="badge bg-success">Habilitado</span>';
        },
    ];

    if (!Yii::$app->user->isGuest) {
        $columns[] = [
            'class'      => ActionColumn::class,
            'template'   => '{view}',
            'urlCreator' => fn($action, $model, $key, $index, $column) =>
                Url::toRoute([$action, 'id' => $model->id]),
            'buttons' => [
                'view' => fn($url) => Html::a(
                    'Ver',
                    $url,
                    ['class' => 'btn btn-primary']
                ),
            ],
        ];
    }
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'pager' => [
            'class'          => \yii\bootstrap5\LinkPager::class,
            'firstPageLabel' => false,
            'lastPageLabel'  => false,
            'prevPageLabel'  => '‹',
            'nextPageLabel'  => '›',
            'maxButtonCount' => 7,
        ],
        'columns' => $columns,
    ]); ?>

    <?php Pjax::end(); ?>

</div>
