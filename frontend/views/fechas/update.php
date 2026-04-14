<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Fechas $model */

$this->title = 'Update Fechas: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Fechas', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="fechas-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model'   => $model,
        'torneos' => $torneos,
        'clubes'  => $clubes,
    ]) ?>

</div>
