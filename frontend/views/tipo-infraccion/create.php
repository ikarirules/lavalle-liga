<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\TipoInfraccion $model */

$this->title = 'Create Tipo Infraccion';
$this->params['breadcrumbs'][] = ['label' => 'Tipo Infraccions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tipo-infraccion-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
