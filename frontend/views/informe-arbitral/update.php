<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\InformeArbitral $model */

$this->title = 'Update Informe Arbitral: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Informe Arbitrals', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="informe-arbitral-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model'           => $model,
        'partidosOptions' => $partidosOptions,
        'isArbitro'       => $isArbitro,
        'tiposOptions'    => $tiposOptions,
        'arbitrosOptions' => $arbitrosOptions,
    ]) ?>

</div>
