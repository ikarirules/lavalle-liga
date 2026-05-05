<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\ListaJugadores $model */
/** @var array  $partidos */
/** @var array  $clubes */
/** @var string $jugadoresJson */

$this->title = 'Editar Entrada #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Lista de Jugadores', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => '#' . $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="lista-jugadores-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model'         => $model,
        'partidos'      => $partidos,
        'clubes'        => $clubes,
        'jugadoresJson' => $jugadoresJson,
    ]) ?>

</div>
