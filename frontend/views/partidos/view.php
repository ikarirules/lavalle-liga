<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Partidos $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Partidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="partidos-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Lista de Jugadores', ['/lista-jugadores/lista-partido', 'partido_id' => $model->id], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '¿Eliminar este partido?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'fecha_id',
            'categoria',
            'club_local_id',
            'dt1_local',
            'dt2_local',
            'club_visitante_id',
            'dt1_visitante',
            'dt2_visitante',
            'cancha',
            'estado',
            'arbitro',
            'asistente1',
            'asistente2',
            'asistente3',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
