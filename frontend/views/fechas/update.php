<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Fechas $model */

$this->title = 'Editar Fecha: ' . $model->numero_fecha;
$this->params['breadcrumbs'][] = ['label' => 'Fechas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->numero_fecha, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="fechas-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model'   => $model,
        'torneos' => $torneos,
        'clubes'  => $clubes,
    ]) ?>

</div>
