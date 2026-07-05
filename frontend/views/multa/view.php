<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var $this yii\web\View */
/** @var $multa common\models\Multa */
/** @var $reincidencia common\models\Multa[] */

$jugador = $multa->jugador;
$this->title = 'Multa #' . $multa->id . ($jugador ? ' — ' . $jugador->nombre : '');
?>
<div class="multa-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="mb-3">
        <?= Html::a('« Volver', ['index'], ['class' => 'btn btn-secondary']) ?>

        <?php if (!$multa->pagado): ?>
            <?= Html::beginForm(['marcar-pagada', 'id' => $multa->id], 'post', ['style' => 'display:inline-block;margin-left:8px']) ?>
            <?= Html::submitButton('Marcar como pagada', [
                'class'   => 'btn btn-success',
                'onclick' => "return confirm('¿Confirmar pago?')",
            ]) ?>
            <?= Html::endForm() ?>
        <?php else: ?>
            <?= Html::beginForm(['desmarcar-pagada', 'id' => $multa->id], 'post', ['style' => 'display:inline-block;margin-left:8px']) ?>
            <?= Html::submitButton('Revertir pago', [
                'class'   => 'btn btn-outline-warning',
                'onclick' => "return confirm('¿Revertir?')",
            ]) ?>
            <?= Html::endForm() ?>
        <?php endif; ?>
    </div>

    <?= DetailView::widget([
        'model' => $multa,
        'attributes' => [
            'id',
            [
                'label' => 'Jugador',
                'value' => $jugador ? $jugador->nombre . ' — DNI ' . $jugador->dni : '—',
            ],
            [
                'label' => 'Club',
                'value' => ($jugador && $jugador->club) ? $jugador->club->nombre : '—',
            ],
            [
                'label' => 'Infracción',
                'value' => ($multa->informeDetalle && $multa->informeDetalle->tipoInfraccion)
                    ? $multa->informeDetalle->tipoInfraccion->nombre : '—',
            ],
            [
                'label' => 'Partido',
                'value' => ($multa->informeDetalle && $multa->informeDetalle->informe && $multa->informeDetalle->informe->partido)
                    ? 'Partido #' . $multa->informeDetalle->informe->partido->id : '—',
            ],
            [
                'attribute' => 'monto',
                'value'     => '$ ' . number_format($multa->monto, 2, ',', '.'),
            ],
            [
                'label'  => 'Estado',
                'format' => 'raw',
                'value'  => $multa->pagado
                    ? '<span class="badge bg-success">Pagada</span>'
                    : '<span class="badge bg-danger">Pendiente</span>',
            ],
            [
                'attribute' => 'fecha_pago',
                'value'     => $multa->fecha_pago ? date('d/m/Y', strtotime($multa->fecha_pago)) : '—',
            ],
            'observaciones',
        ],
    ]) ?>

    <?php if (!empty($reincidencia)): ?>
        <h3 class="mt-4">Historial de multas del jugador (reincidencia: <?= count($reincidencia) ?>)</h3>
        <table class="table table-sm table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Infracción</th>
                    <th>Monto</th>
                    <th>Estado</th>
                    <th>Fecha pago</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($reincidencia as $r): ?>
                <tr>
                    <td><?= Html::a('#' . $r->id, ['view', 'id' => $r->id]) ?></td>
                    <td><?= ($r->informeDetalle && $r->informeDetalle->tipoInfraccion)
                        ? Html::encode($r->informeDetalle->tipoInfraccion->nombre) : '—' ?></td>
                    <td>$ <?= number_format($r->monto, 2, ',', '.') ?></td>
                    <td><?= $r->pagado
                        ? '<span class="badge bg-success">Pagada</span>'
                        : '<span class="badge bg-danger">Pendiente</span>' ?></td>
                    <td><?= $r->fecha_pago ? date('d/m/Y', strtotime($r->fecha_pago)) : '—' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-muted mt-4">Sin antecedentes previos para este jugador.</p>
    <?php endif; ?>
</div>
