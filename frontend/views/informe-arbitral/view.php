<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\InformeArbitral $model */

$partido   = $model->partido;
$local     = $partido && $partido->clubLocal     ? $partido->clubLocal->nombre     : '—';
$visitante = $partido && $partido->clubVisitante ? $partido->clubVisitante->nombre : '—';
$fecha     = $partido ? $partido->fecha : null;

$this->title = 'Informe Arbitral #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Informes Arbitrales', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="informe-arbitral-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Descargar / Imprimir PDF', ['pdf', 'id' => $model->id], [
            'class'  => 'btn btn-success',
            'target' => '_blank',
        ]) ?>
        <?= Html::a('Editar', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Eliminar', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data'  => [
                'confirm' => '¿Eliminar este informe?',
                'method'  => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model'      => $model,
        'attributes' => [
            [
                'label' => 'Partido',
                'value' => $partido ? ($local . ' vs ' . $visitante . ' (' . $partido->categoria . ')') : '—',
            ],
            [
                'label' => 'Fecha',
                'value' => $fecha ? ('Fecha #' . $fecha->numero_fecha . ' — ' . $fecha->fecha_programada) : '—',
            ],
            [
                'label' => 'Árbitro',
                'value' => $model->arbitro ? $model->arbitro->username : '—',
            ],
            [
                'label' => 'Asistente',
                'value' => $model->asistente ? $model->asistente->username : '—',
            ],
            'observaciones:ntext',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

    <h3 class="mt-4">Goles</h3>
    <?php $goles = $model->goles; ?>
    <?php if (empty($goles)): ?>
        <p class="text-muted">Sin goles registrados.</p>
    <?php else: ?>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>Club</th>
                <th>Jugador</th>
                <th class="text-center" style="width:100px">Cantidad</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($goles as $gol): ?>
            <tr>
                <td><?= $gol->club ? Html::encode($gol->club->nombre) : $gol->club_id ?></td>
                <td><?= $gol->jugador ? Html::encode($gol->jugador->nombre) : '—' ?></td>
                <td class="text-center"><?= $gol->cantidad ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <h3 class="mt-4">Infracciones</h3>
    <?php $detalles = $model->detalles; ?>
    <?php if (empty($detalles)): ?>
        <p class="text-muted">Sin infracciones registradas.</p>
    <?php else: ?>
    <table class="table table-bordered table-sm">
        <thead>
            <tr>
                <th>Club</th>
                <th>Jugador</th>
                <th>Tipo de infracción</th>
                <th class="text-center" style="width:90px">Minuto</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detalles as $det): ?>
            <tr>
                <td><?= $det->club ? Html::encode($det->club->nombre) : $det->club_id ?></td>
                <td><?= $det->jugador ? Html::encode($det->jugador->nombre) : '—' ?></td>
                <td><?= $det->tipoInfraccion ? Html::encode($det->tipoInfraccion->nombre) : '—' ?></td>
                <td class="text-center"><?= $det->minuto ?? '—' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

</div>
