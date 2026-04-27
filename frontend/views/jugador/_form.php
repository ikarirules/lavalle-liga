<?php

use common\models\Categoria;
use common\models\Club;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Jugador $model */
/** @var yii\widgets\ActiveForm $form */

$esDirectivoDeClub = Yii::$app->user->can('directivo')
    && !Yii::$app->user->can('miembro_liga')
    && !Yii::$app->user->can('admin_liga');
?>

<div class="jugador-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dni')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fecha_nacimiento')->input('date') ?>

    <?= $form->field($model, 'numero_carnet')->textInput(['maxlength' => true]) ?>

    <?php
    $todasCategorias = Categoria::find()->where(['activo' => 1])->all();
    $categoriasJson  = Json::encode(array_map(fn($c) => [
        'id'          => $c->id,
        'nombre'      => $c->nombre,
        'fecha_desde' => $c->fecha_desde,
        'fecha_hasta' => $c->fecha_hasta,
    ], $todasCategorias));
    ?>
    <?= $form->field($model, 'categoria_id')->dropDownList(
        Categoria::lista(),
        ['prompt' => 'Seleccionar categoría...', 'id' => 'jugador-categoria_id']
    ) ?>
    <div id="categoria-hint" class="mb-3" style="display:none">
        <small class="text-success"><strong id="categoria-hint-texto"></strong></small>
    </div>

    <?php
    $inputFechaId    = Html::getInputId($model, 'fecha_nacimiento');
    $inputCatId      = 'jugador-categoria_id';
    $js = <<<JS
(function () {
    var categorias = {$categoriasJson};
    var fechaInput = document.getElementById('{$inputFechaId}');
    var catSelect  = document.getElementById('{$inputCatId}');
    var hint       = document.getElementById('categoria-hint');
    var hintTexto  = document.getElementById('categoria-hint-texto');

    function actualizarCategoria() {
        var fecha = fechaInput.value;
        if (!fecha) {
            hint.style.display = 'none';
            return;
        }
        var match = null;
        for (var i = 0; i < categorias.length; i++) {
            var c = categorias[i];
            if (c.fecha_desde && c.fecha_hasta && fecha >= c.fecha_desde && fecha <= c.fecha_hasta) {
                match = c;
                break;
            }
        }
        if (match) {
            catSelect.value        = match.id;
            hint.style.display     = 'block';
            hintTexto.textContent  = '✓ Categoría asignada automáticamente: ' + match.nombre;
        } else {
            hint.style.display     = 'block';
            hintTexto.className    = '';
            hintTexto.style.color  = '#6c757d';
            hintTexto.textContent  = 'Ninguna categoría coincide con esa fecha — seleccioná manualmente.';
        }
    }

    fechaInput.addEventListener('change', actualizarCategoria);
    // Ejecutar al cargar si ya hay fecha (edición)
    if (fechaInput.value) actualizarCategoria();
})();
JS;
    $this->registerJs($js);
    ?>

    <?php if ($esDirectivoDeClub): ?>
        <?= $form->field($model, 'club_id')->hiddenInput()->label(false) ?>
        <div class="form-group">
            <label class="control-label">Club</label>
            <p class="form-control-static"><?= Html::encode($model->club ? $model->club->nombre : '') ?></p>
        </div>
    <?php else: ?>
        <?= $form->field($model, 'club_id')->dropDownList(
            ArrayHelper::map(Club::find()->orderBy('nombre')->all(), 'id', 'nombre'),
            ['prompt' => 'Seleccionar club...']
        ) ?>
    <?php endif; ?>

    <?php $clubsLista = ArrayHelper::map(Club::find()->orderBy('nombre')->all(), 'id', 'nombre'); ?>
    <?= $form->field($model, 'club_pase_id')->dropDownList(
        $clubsLista,
        ['prompt' => 'Sin pase (ninguno)']
    )->hint('Solo si el jugador tiene pase de otro club.') ?>

    <?php if (!$esDirectivoDeClub): ?>
    <fieldset>
        <legend>Suspensión</legend>
        <?= $form->field($model, 'numero_fecha_suspension')->textInput(['type' => 'number', 'min' => 0]) ?>
        <?= $form->field($model, 'cant_fechas_suspension')->textInput(['type' => 'number', 'min' => 0]) ?>
    </fieldset>
    <?php endif; ?>

    <fieldset class="mt-3">
        <legend>Foto de Carnet</legend>
        <?php if ($model->foto_carnet): ?>
            <div class="mb-2">
                <?= Html::img($model->fotoUrl, ['style' => 'max-height:150px; border:1px solid #ccc; border-radius:4px']) ?>
            </div>
        <?php endif; ?>
        <?= $form->field($model, 'foto_file')->fileInput(['accept' => 'image/*'])->hint('JPG, PNG o WEBP. Máx. 2MB.') ?>
    </fieldset>

    <div class="form-group mt-3">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
