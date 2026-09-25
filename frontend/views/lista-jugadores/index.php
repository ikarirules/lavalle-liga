<?php

use common\models\Club;
use common\models\ListaJugadores;
use common\models\Partidos;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */

$this->title = 'Listas de Jugadores';
$this->params['breadcrumbs'][] = $this->title;

// Obtener partidos que tienen al menos un jugador en lista
$partidos = Partidos::find()
    ->with(['clubLocal', 'clubVisitante'])
    ->innerJoin('lista_jugadores lj', 'lj.partido_id = {{%partidos}}.id')
    ->groupBy('{{%partidos}}.id')
    ->orderBy(['{{%partidos}}.id' => SORT_DESC])
    ->all();

$clubes = Club::find()->where(['activo' => 1])->orderBy('nombre')->all();
?>
<div class="lista-jugadores-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Agregar jugador', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <h2 class="h4">Listas por partido</h2>

    <?php if (empty($partidos)): ?>
        <div class="alert alert-info">No hay listas de partido cargadas aún.</div>
    <?php else: ?>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Partido</th>
                <th>Categoría</th>
                <th>Local</th>
                <th>Visitante</th>
                <th>Jugadores</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($partidos as $p):
                $cantLocal = ListaJugadores::find()
                    ->where(['partido_id' => $p->id, 'club_id' => $p->club_local_id])
                    ->count();
                $cantVisit = ListaJugadores::find()
                    ->where(['partido_id' => $p->id, 'club_id' => $p->club_visitante_id])
                    ->count();
            ?>
            <tr>
                <td><?= Html::a('Partido #' . $p->id, ['/partidos/view', 'id' => $p->id]) ?></td>
                <td><?= Html::encode($p->categoria) ?></td>
                <td><?= Html::encode($p->clubLocal     ? $p->clubLocal->nombre     : '—') ?></td>
                <td><?= Html::encode($p->clubVisitante ? $p->clubVisitante->nombre : '—') ?></td>
                <td>
                    <span class="badge badge-primary"><?= $cantLocal ?> locales</span>
                    <span class="badge badge-warning"><?= $cantVisit ?> visitantes</span>
                </td>
                <td>
                    <?= Html::a('Ver lista', ['lista-partido', 'partido_id' => $p->id], ['class' => 'btn btn-sm btn-info']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

    <h2 class="h4 mt-4">Planteles (Buena Fe)</h2>
    <p class="text-muted">
        El plantel se carga una sola vez por club y se hereda automáticamente (jugador + remera)
        en cada partido de categorías con Buena Fe habilitada. Solo hay que tocarlo ante una baja o un pase.
    </p>
    <?php if (empty($clubes)): ?>
        <div class="alert alert-info">No hay clubes activos cargados.</div>
    <?php else: ?>
    <table class="table table-bordered table-hover table-sm">
        <thead>
            <tr>
                <th>Club</th>
                <th>Jugadores en plantel</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clubes as $c):
                $cantPlantel = ListaJugadores::find()
                    ->where(['tipo_lista' => ListaJugadores::TIPO_BUENA_FE, 'club_id' => $c->id])
                    ->count();
            ?>
            <tr>
                <td><?= Html::encode($c->nombre) ?></td>
                <td><span class="badge badge-secondary"><?= $cantPlantel ?></span></td>
                <td><?= Html::a('Ver plantel', ['plantel', 'club_id' => $c->id], ['class' => 'btn btn-sm btn-outline-success']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>

</div>
