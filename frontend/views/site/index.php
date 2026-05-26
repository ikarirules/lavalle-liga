<?php

use yii\helpers\Html;
use common\models\Partidos;

/** @var yii\web\View $this */
/** @var common\models\Fechas[] $fechas */

$this->title = 'Lavalle Liga';

$diasEs  = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
$mesesEs = ['', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
             'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

$estadoBadge = [
    Partidos::ESTADO_PROGRAMADA => 'bg-secondary',
    Partidos::ESTADO_JUGADA     => 'bg-success',
    Partidos::ESTADO_SUSPENDIDA => 'bg-warning text-dark',
    Partidos::ESTADO_POSTERGADA => 'bg-danger',
];
?>

<style>
.partido-row {
    display: flex;
    align-items: center;
    gap: .5rem;
    padding: .6rem .75rem;
    border-bottom: 1px solid #44474d;
    color: #d0d0d0;
}
.partido-row:last-child { border-bottom: none; }

.partido-cat  { width: 90px; flex-shrink: 0; font-size: .78rem; color: #6c757d; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
.partido-vs   { display: flex; align-items: center; gap: .4rem; flex: 1; min-width: 0; font-weight: 500; }
.partido-club { flex: 1; min-width: 0; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; }
.partido-sep  { flex-shrink: 0; color: #adb5bd; font-size: .85rem; }
.partido-meta { display: flex; align-items: center; gap: .5rem; flex-shrink: 0; }
.partido-cancha { font-size: .8rem; color: #6c757d; }

@media (max-width: 575.98px) {
    .partido-row  { flex-wrap: wrap; row-gap: .3rem; }
    .partido-cat  { width: auto; }
    .partido-vs   { width: 100%; order: -1; }
    .partido-meta { width: 100%; justify-content: space-between; }
    .partido-cancha { display: none; }
}
</style>

<div class="site-index pt-2">

    <div class="d-flex align-items-baseline gap-3 mb-3">
        <h2 class="mb-0">Próximos partidos</h2>
        <small class="text-muted">hasta el próximo domingo</small>
    </div>

    <?php if (empty($fechas)): ?>
        <div class="alert alert-light border text-muted">
            No hay partidos programados para los próximos días.
        </div>
    <?php else: ?>
        <?php foreach ($fechas as $fecha): ?>
            <?php
                $ts    = strtotime($fecha->fecha_programada);
                $dia   = $diasEs[(int)date('w', $ts)];
                $d     = (int)date('j', $ts);
                $mes   = $mesesEs[(int)date('n', $ts)];
                $esHoy = $fecha->fecha_programada === date('Y-m-d');
            ?>
            <div class="card mb-3 shadow-sm" style="background:#383b40; border-color:#44474d;">
                <div class="card-header d-flex align-items-center gap-2"
                     style="background:#0a0a0a; <?= $esHoy
                        ? 'color:#00f5ff; text-shadow:0 0 8px #00f5ff;'
                        : 'color:#39ff14; text-shadow:0 0 8px #39ff14;' ?>">
                    <span class="fw-semibold">Fecha <?= Html::encode($fecha->numero_fecha) ?></span>
                    <span class="small" style="opacity:.75">
                        &mdash; <?= $dia ?> <?= $d ?> de <?= $mes ?>
                    </span>
                    <?php if ($esHoy): ?>
                        <span class="badge ms-auto" style="background:#00f5ff; color:#0a0a0a;">Hoy</span>
                    <?php endif; ?>
                </div>

                <?php if (empty($fecha->partidos)): ?>
                    <div class="card-body text-muted small">Sin partidos cargados.</div>
                <?php else: ?>
                    <div class="card-body p-0">
                        <?php foreach ($fecha->partidos as $partido): ?>
                            <div class="partido-row">
                                <span class="partido-cat"><?= Html::encode($partido->categoria) ?></span>
                                <div class="partido-vs">
                                    <span class="partido-club text-end"><?= Html::encode($partido->clubLocal?->nombre ?? '—') ?></span>
                                    <span class="partido-sep">vs</span>
                                    <span class="partido-club"><?= Html::encode($partido->clubVisitante?->nombre ?? '—') ?></span>
                                </div>
                                <div class="partido-meta">
                                    <?php if ($partido->cancha): ?>
                                        <span class="partido-cancha">
                                            <i class="bi bi-geo-alt"></i> <?= Html::encode($partido->cancha) ?>
                                        </span>
                                    <?php endif; ?>
                                    <span class="badge <?= $estadoBadge[$partido->estado] ?? 'bg-secondary' ?>">
                                        <?= Html::encode($partido->displayEstado()) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>
