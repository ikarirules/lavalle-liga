<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Torneo $model */

$this->title = 'Editar Torneo: ' . $model->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Torneos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->nombre, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="torneo-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
