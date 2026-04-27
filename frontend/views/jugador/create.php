<?php

/** @var yii\web\View $this */
/** @var common\models\Jugador $model */

$this->title = 'Nuevo Jugador';
$this->params['breadcrumbs'][] = ['label' => 'Jugadores', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="jugador-create">

    <h1><?= $this->title ?></h1>

    <?= $this->render('_form', ['model' => $model]) ?>

</div>
