<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\InformeDetalle $model */

$this->title = 'Create Informe Detalle';
$this->params['breadcrumbs'][] = ['label' => 'Informe Detalles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="informe-detalle-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
