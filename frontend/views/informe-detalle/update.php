<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\InformeDetalle $model */

$this->title = 'Update Informe Detalle: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Informe Detalles', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="informe-detalle-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
