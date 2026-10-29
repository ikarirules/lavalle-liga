<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var array $tablas */
/** @var common\models\Torneo|null $torneo */

$this->title = 'Tabla de Posiciones';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
.tabla-posiciones-card { background:#383b40; border-color:#44474d; }
.tabla-posiciones-card .card-header {
    background:#0a0a0a; color:#39ff14; text-shadow:0 0 8px #39ff14;
}
.tabla-posiciones table { color:#d0d0d0; margin-bottom:0; }
.tabla-posiciones thead th {
    color:#6c757d; font-size:.75rem; text-transform:uppercase; letter-spacing:.04em;
    border-color:#44474d;
}
.tabla-posiciones td, .tabla-posiciones th { border-color:#44474d; }
.tabla-posiciones tbody tr:hover { background:#2f3237; }
.tabla-posiciones .club-pos { width:2.2rem; color:#6c757d; }
.tabla-posiciones .club-pts { font-weight:700; color:#00f5ff; }
/* Primeros 8 puestos (clasifican): fondo azul, los bordes de la tabla se siguen viendo */
.tabla-posiciones tr.clasifica td { background-color:#81BAE3; color:#10273a; }
.tabla-posiciones tr.clasifica td.club-pos { font-weight:700; }
.leyenda-clasifica { display:inline-block; width:.8rem; height:.8rem; vertical-align:middle;
    background:#81BAE3; border:1px solid #44474d; }
</style>

<div class="tabla-posiciones-index pt-2">

    <div class="d-flex align-items-baseline gap-3 mb-3">
        <h2 class="mb-0">Tabla de Posiciones</h2>
        <?php if ($torneo): ?>
            <small class="text-muted"><?= Html::encode($torneo->nombre) ?></small>
        <?php endif; ?>
    </div>

    <?php if (!$torneo): ?>
        <div class="alert alert-light border text-muted">
            No hay un torneo activo configurado.
        </div>
    <?php elseif (empty($tablas)): ?>
        <div class="alert alert-light border text-muted">
            No hay categorías activas.
        </div>
    <?php else: ?>
        <?php foreach ($tablas as $tabla): ?>
            <?php $categoria = $tabla['categoria']; $clubes = $tabla['clubes']; ?>
            <div class="card mb-4 shadow-sm tabla-posiciones-card">
                <div class="card-header fw-semibold">
                    <?= Html::encode($categoria->nombre) ?>
                </div>
                <div class="card-body p-0 tabla-posiciones">
                    <?php if (empty($clubes)): ?>
                        <div class="p-3 text-muted small">Sin partidos cargados para esta categoría.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0 align-middle">
                                <thead>
                                    <tr>
                                        <th class="club-pos">#</th>
                                        <th>Club</th>
                                        <th class="text-center">PJ</th>
                                        <th class="text-center">PG</th>
                                        <th class="text-center">PE</th>
                                        <th class="text-center">PP</th>
                                        <th class="text-center">GF</th>
                                        <th class="text-center">GC</th>
                                        <th class="text-center">DG</th>
                                        <th class="text-center">Pts</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($clubes as $i => $fila): ?>
                                        <tr class="<?= $i < 8 ? 'clasifica' : '' ?>">
                                            <td class="club-pos"><?= $i + 1 ?></td>
                                            <td><?= Html::encode($fila['club']->nombre) ?></td>
                                            <td class="text-center"><?= $fila['pj'] ?></td>
                                            <td class="text-center"><?= $fila['pg'] ?></td>
                                            <td class="text-center"><?= $fila['pe'] ?></td>
                                            <td class="text-center"><?= $fila['pp'] ?></td>
                                            <td class="text-center"><?= $fila['gf'] ?></td>
                                            <td class="text-center"><?= $fila['gc'] ?></td>
                                            <td class="text-center"><?= $fila['gf'] - $fila['gc'] ?></td>
                                            <td class="text-center club-pts"><?= $fila['pts'] ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="small text-muted mt-2">
            <strong>PJ</strong> Partidos Jugados &nbsp;·&nbsp;
            <strong>PG</strong> Partidos Ganados &nbsp;·&nbsp;
            <strong>PE</strong> Partidos Empatados &nbsp;·&nbsp;
            <strong>PP</strong> Partidos Perdidos &nbsp;·&nbsp;
            <strong>GF</strong> Goles a Favor &nbsp;·&nbsp;
            <strong>GC</strong> Goles en Contra &nbsp;·&nbsp;
            <strong>DG</strong> Diferencia de Goles &nbsp;·&nbsp;
            <strong>Pts</strong> Puntos &nbsp;·&nbsp;
            <span class="leyenda-clasifica"></span> Primeros 8 puestos
        </div>
    <?php endif; ?>

</div>
