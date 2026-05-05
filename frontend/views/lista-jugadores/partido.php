<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Partidos         $partido */
/** @var common\models\ListaJugadores[] $locales */
/** @var common\models\ListaJugadores[] $visitantes */

$localNombre     = $partido->clubLocal     ? $partido->clubLocal->nombre     : 'Local';
$visitanteNombre = $partido->clubVisitante ? $partido->clubVisitante->nombre : 'Visitante';

$this->title = 'Lista del Partido #' . $partido->id;
$this->params['breadcrumbs'][] = ['label' => 'Lista de Jugadores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lista-jugadores-partido">

    <h1><?= Html::encode($this->title) ?></h1>
    <p class="text-muted">
        <?= Html::encode($localNombre) ?> vs <?= Html::encode($visitanteNombre) ?>
        &mdash; <?= Yii::$app->formatter->asDate($partido->fecha ? $partido->fecha->fecha_programada : null, 'dd/MM/yyyy') ?>
    </p>

    <p>
        <?= Html::a('Agregar jugador local', ['create', 'Partidos[partido_id]' => $partido->id], ['class' => 'btn btn-sm btn-primary']) ?>
        <?= Html::a('Ver partido', ['/partidos/view', 'id' => $partido->id], ['class' => 'btn btn-sm btn-default']) ?>
    </p>

    <div class="row">

        <!-- LOCALES -->
        <div class="col-md-6">
            <h3><?= Html::encode($localNombre) ?> <small>(Local)</small></h3>
            <table class="table table-bordered table-condensed">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Remera</th>
                        <th>Jugador</th>
                        <th>DNI</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($locales)): ?>
                        <tr><td colspan="5" class="text-center text-muted">Sin jugadores cargados</td></tr>
                    <?php else: ?>
                        <?php foreach ($locales as $i => $entry): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= $entry->remera_local ?? '—' ?></td>
                            <td><?= Html::encode($entry->jugador ? $entry->jugador->nombre : '—') ?></td>
                            <td><?= Html::encode($entry->jugador ? $entry->jugador->dni : '—') ?></td>
                            <td>
                                <?= Html::a('✏', ['update', 'id' => $entry->id], ['title' => 'Editar']) ?>
                                <?= Html::a('✕', ['delete', 'id' => $entry->id], [
                                    'title' => 'Eliminar',
                                    'data'  => ['confirm' => '¿Eliminar?', 'method' => 'post'],
                                ]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- VISITANTES -->
        <div class="col-md-6">
            <h3><?= Html::encode($visitanteNombre) ?> <small>(Visitante)</small></h3>
            <table class="table table-bordered table-condensed">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Remera</th>
                        <th>Jugador</th>
                        <th>DNI</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($visitantes)): ?>
                        <tr><td colspan="5" class="text-center text-muted">Sin jugadores cargados</td></tr>
                    <?php else: ?>
                        <?php foreach ($visitantes as $i => $entry): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= $entry->remera_visitante ?? '—' ?></td>
                            <td><?= Html::encode($entry->jugador ? $entry->jugador->nombre : '—') ?></td>
                            <td><?= Html::encode($entry->jugador ? $entry->jugador->dni : '—') ?></td>
                            <td>
                                <?= Html::a('✏', ['update', 'id' => $entry->id], ['title' => 'Editar']) ?>
                                <?= Html::a('✕', ['delete', 'id' => $entry->id], [
                                    'title' => 'Eliminar',
                                    'data'  => ['confirm' => '¿Eliminar?', 'method' => 'post'],
                                ]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div><!-- /.row -->

</div>
