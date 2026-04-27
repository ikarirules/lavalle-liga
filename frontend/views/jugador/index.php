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

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel'  => $searchModel,
        'columns' => [
            'nombre',
            'dni',
            [
                'attribute' => 'fecha_nacimiento',
                'label'     => 'F. Nacimiento',
                'format'    => 'date',
            ],
            [
                'attribute' => 'categoria_id',
                'label'     => 'Categoría',
                'value'     => fn($model) => $model->categoria ? $model->categoria->nombre : '-',
                'filter'    => \common\models\Categoria::lista(),
            ],
            [
                'attribute' => 'club_id',
                'label'     => 'Club',
                'value'     => fn($model) => $model->club ? $model->club->nombre : '-',
                'filter'    => ArrayHelper::map(
                    Club::find()->orderBy('nombre')->all(),
                    'id', 'nombre'
                ),
            ],
            [
                'attribute' => 'club_pase_id',
                'label'     => 'Pase',
                'value'     => fn($model) => $model->clubPase ? $model->clubPase->nombre : '-',
                'filter'    => ArrayHelper::map(
                    Club::find()->orderBy('nombre')->all(),
                    'id', 'nombre'
                ),
            ],
            [
                'label'  => 'Estado',
                'format' => 'raw',
                'value'  => function ($model) {
                    if ($model->suspendido) {
                        return '<span class="badge bg-danger">Suspendido</span>';
                    }
                    return '<span class="badge bg-success">Habilitado</span>';
                },
            ],
            [
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
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
