<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\PartidosSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="partidos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'fecha_id') ?>

    <?= $form->field($model, 'categoria') ?>

    <?= $form->field($model, 'club_local_id') ?>

    <?= $form->field($model, 'club_visitante_id') ?>

    <?php // echo $form->field($model, 'cancha') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 'goles_local') ?>

    <?php // echo $form->field($model, 'goles_visitante') ?>

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
