<?php

use common\models\InformeArbitral;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var frontend\models\InformeArbitralSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Informe Arbitrals';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="informe-arbitral-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Informe Arbitral', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'partido_id',
            'arbitro_id',
            'asistente_id',
            'observaciones:ntext',
            //'created_by',
            //'updated_by',
            //'created_at',
            //'updated_at',
            [
                'class' => ActionColumn::className(),
                'template' => '{view} {update} {pdf} {delete}',
                'buttons' => [
                    'pdf' => function ($url, InformeArbitral $model) {
                        return Html::a(
                            '<i class="bi bi-file-earmark-arrow-down"></i>',
                            Url::toRoute(['pdf', 'id' => $model->id]),
                            ['title' => 'Descargar informe', 'target' => '_blank', 'data-pjax' => '0']
                        );
                    },
                ],
                'urlCreator' => function ($action, InformeArbitral $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ],
        ],
    ]); ?>


</div>
