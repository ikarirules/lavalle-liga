<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Fechas $model */
/** @var yii\widgets\ActiveForm $form */
/** @var array $torneos */
/** @var array $clubes */
/** @var common\models\Categoria[] $categorias */

// Flatpickr
$this->registerCss('.flatpickr-input { background-color: #fff !important; }');
$this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js', ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerJsFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js',       ['depends' => [\yii\web\JqueryAsset::class]]);
$this->registerCssFile('https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css');
$this->registerJs('flatpickr("#fechas-fecha_programada", { dateFormat: "Y-m-d", allowInput: true, locale: "es" });');
$this->registerJs(<<<JS
    $('#sel-todas').on('click', function(e) {
        e.preventDefault();
        $('.cat-check').prop('checked', true);
    });
    $('#sel-ninguna').on('click', function(e) {
        e.preventDefault();
        $('.cat-check').prop('checked', false);
    });
JS);
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

    <?php if (!empty($categorias)): ?>
    <div class="mb-3">
        <label class="form-label fw-bold">Categorías del partido</label>
        <div class="row row-cols-2 row-cols-md-4 g-1 mb-2">
            <?php foreach ($categorias as $cat): ?>
            <div class="col">
                <div class="form-check">
                    <input class="form-check-input cat-check" type="checkbox"
                           name="categorias[]"
                           value="<?= Html::encode($cat->nombre) ?>"
                           id="cat_<?= $cat->id ?>" checked>
                    <label class="form-check-label" for="cat_<?= $cat->id ?>">
                        <?= Html::encode($cat->nombre) ?>
                    </label>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <small>
            <a href="#" id="sel-todas">Todas</a> |
            <a href="#" id="sel-ninguna">Ninguna</a>
        </small>
    </div>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
