<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\Jugador $model */

$this->title = $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Jugadores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jugador-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (Yii::$app->user->can('editarJugadores')): ?>
            <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        <?php if (Yii::$app->user->can('admin_liga')): ?>
            <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data'  => [
                    'confirm' => '¿Estás seguro de eliminar este jugador?',
                    'method'  => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </p>

    <?php if ($model->fotoUrl): ?>
        <div class="mb-3">
            <?= Html::img($model->fotoUrl, [
                'style' => 'max-height:200px; border:1px solid #ccc; border-radius:6px',
                'alt'   => 'Foto carnet ' . Html::encode($model->nombre),
            ]) ?>
        </div>
    <?php endif; ?>

    <?= DetailView::widget([
        'model'      => $model,
        'attributes' => [
            'id',
            'nombre',
            'dni',
            'fecha_nacimiento:date:Fecha de Nacimiento',
            'numero_carnet:text:N° Carnet',
            [
                'label' => 'Categoría',
                'value' => $model->categoria ? $model->categoria->nombre : '-',
            ],
            [
                'label' => 'Club',
                'value' => $model->club ? $model->club->nombre : '-',
            ],
            [
                'label' => 'Club del Pase',
                'value' => $model->clubPase ? $model->clubPase->nombre : '-',
            ],
            [
                'label'  => 'Estado',
                'format' => 'raw',
                'value'  => $model->suspendido
                    ? '<span class="label label-danger">Suspendido</span>'
                    : '<span class="label label-success">Habilitado</span>',
            ],
            'numero_fecha_suspension:integer:N° Fecha Suspensión',
            'cant_fechas_suspension:integer:Fechas de Suspensión',
        ],
    ]) ?>

</div>
