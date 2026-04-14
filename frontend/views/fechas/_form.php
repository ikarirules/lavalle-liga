<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Fechas $model */
/** @var yii\widgets\ActiveForm $form */
/** @var array $torneos */
/** @var array $clubes */

// Flatpickr
$this->registerCss('.flatpickr-input { background-color: #fff !important; }');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js',       ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerCssFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
$this->registerJs('flatpickr("#fechas-fecha_programada", { dateFormat: "Y-m-d", allowInput: true, locale: "es" });');
?>

<div class="fechas-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'numero_fecha')->textInput() ?>

    <?= $form->field($model, 'torneo_id')->dropDownList($torneos, [
        'prompt' => '-- Seleccionar torneo --',
    ]) ?>

    <?= $form->field($model, 'fecha_programada')->textInput(['placeholder' => 'AAAA-MM-DD']) ?>

    <?= $form->field($model, 'fecha_reprogramada_1')->textInput() ?>

    <?= $form->field($model, 'fecha_reprogramada_2')->textInput() ?>

    <?= $form->field($model, 'fecha_jugada')->textInput() ?>

    <?= $form->field($model, 'club_local_id')->dropDownList($clubes, ['prompt' => '-- Club local --']) ?>

    <?= $form->field($model, 'club_visitante_id')->dropDownList($clubes, ['prompt' => '-- Club visitante --']) ?>

    <?= $form->field($model, 'observaciones')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
