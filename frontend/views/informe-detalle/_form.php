<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\InformeDetalle $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="informe-detalle-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'informe_id')->textInput() ?>

    <?= $form->field($model, 'minuto')->textInput() ?>

    <?= $form->field($model, 'jugador_id')->textInput() ?>

    <?= $form->field($model, 'numero_camiseta')->textInput() ?>

    <?= $form->field($model, 'club_id')->textInput() ?>

    <?= $form->field($model, 'tipo_infraccion_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
