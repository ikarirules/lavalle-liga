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

$fechaNac = $model->fecha_nacimiento;
$fechaDia = $fechaMes = $fechaAnio = '';
if ($fechaNac) {
    $parts = explode('-', $fechaNac);
    if (count($parts) === 3) {
        [$fechaAnio, $fechaMes, $fechaDia] = $parts;
    }
}

$anioActual = (int)date('Y');
$anios = [];
for ($y = $anioActual; $y >= 1940; $y--) {
    $anios[$y] = $y;
}
$meses = [
    '01' => 'Enero',    '02' => 'Febrero',  '03' => 'Marzo',
    '04' => 'Abril',    '05' => 'Mayo',      '06' => 'Junio',
    '07' => 'Julio',    '08' => 'Agosto',    '09' => 'Septiembre',
    '10' => 'Octubre',  '11' => 'Noviembre', '12' => 'Diciembre',
];
$dias = [];
for ($d = 1; $d <= 31; $d++) {
    $dias[sprintf('%02d', $d)] = $d;
}

$todasCategorias = Categoria::find()->where(['activo' => 1])->all();
$categoriasJson  = Json::encode(array_map(fn($c) => [
    'id'          => $c->id,
    'nombre'      => $c->nombre,
    'fecha_desde' => $c->fecha_desde,
    'fecha_hasta' => $c->fecha_hasta,
], $todasCategorias));
?>

<div class="jugador-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'nombre')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'dni')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-3">
                <label class="form-label"><?= $model->getAttributeLabel('fecha_nacimiento') ?></label>
                <?= $form->field($model, 'fecha_nacimiento')
                    ->hiddenInput(['id' => 'jugador-fecha_nacimiento'])
                    ->label(false)
                    ->error(['tag' => 'div', 'class' => 'invalid-feedback d-block']) ?>
                <div class="row g-2">
                    <div class="col-4">
                        <?= Html::dropDownList('fecha_dia', $fechaDia, $dias, [
                            'id'    => 'fecha-dia',
                            'class' => 'form-select',
                            'prompt' => 'Día',
                        ]) ?>
                    </div>
                    <div class="col-4">
                        <?= Html::dropDownList('fecha_mes', $fechaMes, $meses, [
                            'id'    => 'fecha-mes',
                            'class' => 'form-select',
                            'prompt' => 'Mes',
                        ]) ?>
                    </div>
                    <div class="col-4">
                        <?= Html::dropDownList('fecha_anio', $fechaAnio, $anios, [
                            'id'    => 'fecha-anio',
                            'class' => 'form-select',
                            'prompt' => 'Año',
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'numero_carnet')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'categoria_id')->dropDownList(
                Categoria::lista(),
                ['prompt' => 'Seleccionar categoría...', 'id' => 'jugador-categoria_id']
            ) ?>
            <div id="categoria-hint" class="mb-3 mt-n2" style="display:none">
                <small><strong id="categoria-hint-texto"></strong></small>
            </div>
        </div>
        <div class="col-md-6">
            <?php if ($esDirectivoDeClub): ?>
                <?= $form->field($model, 'club_id')->hiddenInput()->label(false) ?>
                <div class="mb-3">
                    <label class="form-label">Club</label>
                    <p class="form-control-static"><?= Html::encode($model->club ? $model->club->nombre : '') ?></p>
                </div>
            <?php else: ?>
                <?= $form->field($model, 'club_id')->dropDownList(
                    ArrayHelper::map(Club::find()->orderBy('nombre')->all(), 'id', 'nombre'),
                    ['prompt' => 'Seleccionar club...']
                ) ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?php $clubsLista = ArrayHelper::map(Club::find()->orderBy('nombre')->all(), 'id', 'nombre'); ?>
            <?= $form->field($model, 'club_pase_id')->dropDownList(
                $clubsLista,
                ['prompt' => 'Sin pase (ninguno)']
            )->hint('Solo si el jugador tiene pase de otro club.') ?>
        </div>
    </div>

    <?php if (!$esDirectivoDeClub): ?>
    <fieldset>
        <legend>Suspensión</legend>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'numero_fecha_suspension')->textInput(['type' => 'number', 'min' => 0]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'cant_fechas_suspension')->textInput(['type' => 'number', 'min' => 0]) ?>
            </div>
        </div>
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

    <?php
    $js = <<<JS
(function () {
    var categorias  = {$categoriasJson};
    var fechaInput  = document.getElementById('jugador-fecha_nacimiento');
    var catSelect   = document.getElementById('jugador-categoria_id');
    var hint        = document.getElementById('categoria-hint');
    var hintTexto   = document.getElementById('categoria-hint-texto');
    var diaSel      = document.getElementById('fecha-dia');
    var mesSel      = document.getElementById('fecha-mes');
    var anioSel     = document.getElementById('fecha-anio');

    function actualizarFechaOculta() {
        var dia  = diaSel.value;
        var mes  = mesSel.value;
        var anio = anioSel.value;
        if (dia && mes && anio) {
            fechaInput.value = anio + '-' + mes + '-' + dia;
        } else {
            fechaInput.value = '';
        }
        fechaInput.dispatchEvent(new Event('change'));
    }

    function actualizarCategoria() {
        var fecha = fechaInput.value;
        if (!fecha) { hint.style.display = 'none'; return; }
        var match = null;
        for (var i = 0; i < categorias.length; i++) {
            var c = categorias[i];
            if (c.fecha_desde && c.fecha_hasta && fecha >= c.fecha_desde && fecha <= c.fecha_hasta) {
                match = c; break;
            }
        }
        hint.style.display = 'block';
        if (match) {
            catSelect.value       = match.id;
            hintTexto.style.color = '';
            hintTexto.className   = 'text-success';
            hintTexto.textContent = '✓ Categoría asignada automáticamente: ' + match.nombre;
        } else {
            hintTexto.className   = '';
            hintTexto.style.color = '#6c757d';
            hintTexto.textContent = 'Ninguna categoría coincide con esa fecha — seleccioná manualmente.';
        }
    }

    diaSel.addEventListener('change', actualizarFechaOculta);
    mesSel.addEventListener('change', actualizarFechaOculta);
    anioSel.addEventListener('change', actualizarFechaOculta);
    fechaInput.addEventListener('change', actualizarCategoria);

    if (fechaInput.value) actualizarCategoria();
})();
JS;
    $this->registerJs($js);
    ?>

    <?php ActiveForm::end(); ?>

</div>
