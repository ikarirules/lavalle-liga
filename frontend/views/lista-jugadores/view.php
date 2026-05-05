<?php

use common\models\ListaJugadores;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\ListaJugadores $model */

$this->title = 'Entrada #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Lista de Jugadores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="lista-jugadores-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data'  => ['confirm' => '¿Eliminar este registro?', 'method' => 'post'],
        ]) ?>
        <?php if ($model->partido_id): ?>
            <?= Html::a('Ver lista del partido', ['partido', 'partido_id' => $model->partido_id], ['class' => 'btn btn-info']) ?>
        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model'      => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'tipo_lista',
                'value'     => ListaJugadores::optsTipoLista()[$model->tipo_lista] ?? $model->tipo_lista,
            ],
            [
                'label' => 'Partido',
                'value' => $model->partido
                    ? 'Partido #' . $model->partido->id
                      . ' — ' . ($model->partido->clubLocal  ? $model->partido->clubLocal->nombre  : '?')
                      . ' vs '  . ($model->partido->clubVisitante ? $model->partido->clubVisitante->nombre : '?')
                    : '—',
            ],
            [
                'attribute' => 'club_id',
                'value'     => $model->club ? $model->club->nombre : '—',
                'label'     => 'Club',
            ],
            [
                'attribute' => 'jugador_id',
                'value'     => $model->jugador ? $model->jugador->nombre . ' (DNI: ' . $model->jugador->dni . ')' : '—',
                'label'     => 'Jugador',
            ],
            'remera_local',
            'remera_visitante',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
