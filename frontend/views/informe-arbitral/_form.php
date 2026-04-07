<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\InformeArbitral $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="informe-arbitral-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'partido_id')->textInput() ?>

    <?= $form->field($model, 'arbitro_id')->textInput() ?>

    <?= $form->field($model, 'asistente_id')->textInput() ?>

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
