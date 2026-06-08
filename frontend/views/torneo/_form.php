<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Torneo $model */
/** @var yii\widgets\ActiveForm $form */

$this->registerCss('.flatpickr-input { background-color: #fff !important; }');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerCssFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
$this->registerJs(<<<JS
    flatpickr("#torneo-fecha_inicio", { dateFormat: "Y-m-d", allowInput: true, locale: "es" });
    flatpickr("#torneo-fecha_fin",    { dateFormat: "Y-m-d", allowInput: true, locale: "es" });
JS);
?>

<div class="torneo-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-12">
            <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'anio')->textInput() ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'activo')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'fecha_inicio')->textInput(['placeholder' => 'AAAA-MM-DD']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'fecha_fin')->textInput(['placeholder' => 'AAAA-MM-DD']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
