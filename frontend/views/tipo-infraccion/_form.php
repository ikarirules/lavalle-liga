<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\TipoInfraccion $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tipo-infraccion-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'descripcion')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'sancion_descripcion')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sancion_fechas_min')->textInput() ?>

    <?= $form->field($model, 'sancion_fechas_max')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
