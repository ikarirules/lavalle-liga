<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\FechasSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="fechas-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'numero_fecha') ?>

    <?= $form->field($model, 'torneo_id') ?>

    <?= $form->field($model, 'fecha_programada') ?>

    <?= $form->field($model, 'fecha_reprogramada_1') ?>

    <?php // echo $form->field($model, 'fecha_reprogramada_2') ?>

    <?php // echo $form->field($model, 'fecha_jugada') ?>

    <?php // echo $form->field($model, 'club_local_id') ?>

    <?php // echo $form->field($model, 'club_visitante_id') ?>

    <?php // echo $form->field($model, 'arbitro_id') ?>

    <?php // echo $form->field($model, 'observaciones') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
