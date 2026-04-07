<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Partidos $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="partidos-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fecha_id')->textInput() ?>

    <?= $form->field($model, 'categoria')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'club_local_id')->textInput() ?>

    <?= $form->field($model, 'club_visitante_id')->textInput() ?>

    <?= $form->field($model, 'cancha')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'estado')->dropDownList([ 'programada' => 'Programada', 'suspendida' => 'Suspendida', 'postergada' => 'Postergada', 'jugada' => 'Jugada', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'goles_local')->textInput() ?>

    <?= $form->field($model, 'goles_visitante')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
