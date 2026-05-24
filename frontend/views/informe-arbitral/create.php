<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\InformeArbitral $model */

$this->title = 'Create Informe Arbitral';
$this->params['breadcrumbs'][] = ['label' => 'Informe Arbitrals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="informe-arbitral-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model'           => $model,
        'partidosOptions' => $partidosOptions,
        'isArbitro'       => $isArbitro,
        'tiposOptions'    => $tiposOptions,
        'arbitrosOptions' => $arbitrosOptions,
    ]) ?>

</div>
