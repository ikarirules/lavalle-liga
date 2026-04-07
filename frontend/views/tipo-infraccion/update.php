<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\TipoInfraccion $model */

$this->title = 'Update Tipo Infraccion: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tipo Infraccions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tipo-infraccion-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
