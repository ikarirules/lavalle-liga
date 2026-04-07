<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var frontend\models\ClubSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="club-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'nombre') ?>

    <?= $form->field($model, 'razon_social') ?>

    <?= $form->field($model, 'cuit_cuil') ?>

    <?= $form->field($model, 'zona') ?>

    <?php // echo $form->field($model, 'direccion') ?>

    <?php // echo $form->field($model, 'telefono') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'presidente') ?>

    <?php // echo $form->field($model, 'estadio') ?>

    <?php // echo $form->field($model, 'anio_fundacion') ?>

    <?php // echo $form->field($model, 'logo') ?>

    <?php // echo $form->field($model, 'instagram') ?>

    <?php // echo $form->field($model, 'facebook') ?>

    <?php // echo $form->field($model, 'color_primario') ?>

    <?php // echo $form->field($model, 'color_secundario') ?>

    <?php // echo $form->field($model, 'activo') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
