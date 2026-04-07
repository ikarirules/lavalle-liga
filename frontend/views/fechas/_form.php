<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Fechas $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="fechas-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'numero_fecha')->textInput() ?>

    <?= $form->field($model, 'torneo_id')->textInput() ?>

    <?= $form->field($model, 'fecha_programada')->textInput() ?>

    <?= $form->field($model, 'fecha_reprogramada_1')->textInput() ?>

    <?= $form->field($model, 'fecha_reprogramada_2')->textInput() ?>

    <?= $form->field($model, 'fecha_jugada')->textInput() ?>

    <?= $form->field($model, 'club_local_id')->textInput() ?>

    <?= $form->field($model, 'club_visitante_id')->textInput() ?>

    <?= $form->field($model, 'arbitro_id')->textInput() ?>

    <?= $form->field($model, 'observaciones')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
