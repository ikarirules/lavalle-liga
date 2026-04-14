<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Club $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="club-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'razon_social')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cuit_cuil')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'zona')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'direccion')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'telefono')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'presidente')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'estadio')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'anio_fundacion')->textInput() ?>

    <?php // $form->field($model, 'logo')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'instagram')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'facebook')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'color_primario')->textInput(['maxlength' => true]) ?>

    <?php // $form->field($model, 'color_secundario')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'activo')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
