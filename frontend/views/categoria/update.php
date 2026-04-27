<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Categoria $model */

$this->title = 'Editar Categoría: ' . Html::encode($model->nombre);
$this->params['breadcrumbs'][] = ['label' => 'Categorías', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="categoria-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'permite_bf')->checkbox() ?>

    <?= $form->field($model, 'activo')->checkbox() ?>

    <fieldset class="mt-3">
        <legend>Rango de nacimiento</legend>
        <p class="text-muted small">Dejá en blanco si la categoría no tiene restricción de edad. El rango típico es 01/07/AAAA – 30/06/AAAA.</p>
        <?= $form->field($model, 'fecha_desde')->input('date') ?>
        <?= $form->field($model, 'fecha_hasta')->input('date') ?>
    </fieldset>

    <div class="form-group mt-3">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancelar', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
