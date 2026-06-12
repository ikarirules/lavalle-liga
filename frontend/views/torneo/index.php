<?php

use common\models\Torneo;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\TorneoSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Torneos';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="torneo-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->user->can('miembro_liga')): ?>
    <p>
        <?= Html::a('Nuevo Torneo', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php endif; ?>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'nombre',
            'anio',
            'fecha_inicio',
            'fecha_fin',
            //'activo',
            //'created_by',
            //'updated_by',
            //'created_at',
            //'updated_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, Torneo $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
                'visibleButtons' => [
                    'update' => Yii::$app->user->can('miembro_liga'),
                    'delete' => Yii::$app->user->can('admin_liga'),
                ],
            ],
        ],
    ]); ?>


</div>
