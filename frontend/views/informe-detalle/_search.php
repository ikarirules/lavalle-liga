<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\InformeDetalleSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="informe-detalle-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'informe_id') ?>

    <?= $form->field($model, 'minuto') ?>

    <?= $form->field($model, 'jugador_id') ?>

    <?= $form->field($model, 'numero_camiseta') ?>

    <?php // echo $form->field($model, 'club_id') ?>

    <?php // echo $form->field($model, 'tipo_infraccion_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
