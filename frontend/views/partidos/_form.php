<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Partidos $model */
/** @var yii\widgets\ActiveForm $form */
/** @var array $fechasOptions   [id => "Fecha #N — dd/mm/yyyy"] */
/** @var array $fechasData          [id => [fecha_programada, club_local_id, ...]] */
/** @var bool  $isArbitro           true si el usuario logueado tiene rol árbitro */
/** @var array $arbitros            [username => username] — vacío si $isArbitro */
/** @var array $categorias          [nombre => nombre] */
/** @var array $directivosPorClub   [club_id => [username => username]] */

$currentFechaData = ($model->fecha_id && isset($fechasData[$model->fecha_id]))
    ? $fechasData[$model->fecha_id]
    : null;

$clubLocalId     = $currentFechaData['club_local_id']     ?? null;
$clubVisitanteId = $currentFechaData['club_visitante_id'] ?? null;
$dtLocalOpts     = array_merge(['' => '-- Sin DT --'], $directivosPorClub[$clubLocalId]     ?? []);
$dtVisitanteOpts = array_merge(['' => '-- Sin DT --'], $directivosPorClub[$clubVisitanteId] ?? []);
?>

<div class="partidos-form">

    <?php $form = ActiveForm::begin(); ?>

    <!-- Fecha / Fecha programada -->
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'fecha_id')->dropDownList($fechasOptions, [
                'prompt' => '-- Seleccionar fecha --',
                'id'     => 'partido-fecha_id',
            ])->label('Fecha') ?>
        </div>
        <div class="col-md-6">
            <div class="form-group" id="fecha-programada-wrapper"<?= $currentFechaData ? '' : ' style="display:none"' ?>>
                <label class="control-label">Fecha Programada</label>
                <input type="text" id="fecha-programada-display" class="form-control" readonly
                       value="<?= $currentFechaData ? Html::encode($currentFechaData['fecha_programada']) : '' ?>">
            </div>
        </div>
    </div>

    <!-- Categoría / Estado -->
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'categoria')->dropDownList($categorias, ['prompt' => '-- Seleccionar categoría --']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'estado')->dropDownList([
                'programada' => 'Programada',
                'suspendida' => 'Suspendida',
                'postergada' => 'Postergada',
                'jugada'     => 'Jugada',
            ]) ?>
        </div>
    </div>

    <!-- Cancha / Árbitro -->
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'cancha')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?php if ($isArbitro): ?>
                <?= $form->field($model, 'arbitro')->textInput(['readonly' => true])->label('Árbitro') ?>
            <?php else: ?>
                <?= $form->field($model, 'arbitro')->dropDownList($arbitros, [
                    'prompt' => '-- Seleccionar árbitro --',
                ])->label('Árbitro') ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Asistentes -->
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'asistente1')->textInput(['maxlength' => true])->label('Asistente 1') ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'asistente2')->textInput(['maxlength' => true])->label('Asistente 2') ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'asistente3')->textInput(['maxlength' => true])->label('Asistente 3') ?>
        </div>
    </div>

    <!-- Clubs (hidden inputs + displays de solo lectura) -->
    <?= $form->field($model, 'club_local_id')->hiddenInput(['id' => 'partido-club_local_id'])->label(false) ?>
    <?= $form->field($model, 'club_visitante_id')->hiddenInput(['id' => 'partido-club_visitante_id'])->label(false) ?>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Club Local</label>
                <input type="text" id="club-local-display" class="form-control" readonly
                       value="<?= $currentFechaData ? Html::encode($currentFechaData['club_local_nombre']) : '' ?>"
                       placeholder="Se completa al seleccionar la fecha">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">Club Visitante</label>
                <input type="text" id="club-visitante-display" class="form-control" readonly
                       value="<?= $currentFechaData ? Html::encode($currentFechaData['club_visitante_nombre']) : '' ?>"
                       placeholder="Se completa al seleccionar la fecha">
            </div>
        </div>
    </div>

    <!-- DT Local / DT Visitante -->
    <div class="row">
        <div class="col-md-3 col-sm-6">
            <?= $form->field($model, 'dt1_local')->dropDownList($dtLocalOpts, ['id' => 'dt1-local'])->label('DT 1 Local') ?>
        </div>
        <div class="col-md-3 col-sm-6">
            <?= $form->field($model, 'dt2_local')->dropDownList($dtLocalOpts, ['id' => 'dt2-local'])->label('DT 2 Local') ?>
        </div>
        <div class="col-md-3 col-sm-6">
            <?= $form->field($model, 'dt1_visitante')->dropDownList($dtVisitanteOpts, ['id' => 'dt1-visitante'])->label('DT 1 Visitante') ?>
        </div>
        <div class="col-md-3 col-sm-6">
            <?= $form->field($model, 'dt2_visitante')->dropDownList($dtVisitanteOpts, ['id' => 'dt2-visitante'])->label('DT 2 Visitante') ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$fechasJson     = Json::encode($fechasData);
$directivosJson = Json::encode($directivosPorClub);
$js = <<<JS
var fechasData = $fechasJson;
var directivosPorClub = $directivosJson;

function buildDtOpts(clubId, selectedVal) {
    var opts = '<option value="">-- Sin DT --</option>';
    var lista = directivosPorClub[clubId];
    if (lista) {
        $.each(lista, function(username, label) {
            var sel = (username === selectedVal) ? ' selected' : '';
            opts += '<option value="' + username + '"' + sel + '>' + label + '</option>';
        });
    }
    return opts;
}

$('#partido-fecha_id').on('change', function () {
    var fid = $(this).val();
    if (fid && fechasData[fid]) {
        var f = fechasData[fid];
        $('#fecha-programada-wrapper').show();
        $('#fecha-programada-display').val(f.fecha_programada);
        $('#partido-club_local_id').val(f.club_local_id);
        $('#club-local-display').val(f.club_local_nombre);
        $('#partido-club_visitante_id').val(f.club_visitante_id);
        $('#club-visitante-display').val(f.club_visitante_nombre);
        var dtLocalOpts     = buildDtOpts(f.club_local_id, '');
        var dtVisitanteOpts = buildDtOpts(f.club_visitante_id, '');
        $('#dt1-local, #dt2-local').html(dtLocalOpts);
        $('#dt1-visitante, #dt2-visitante').html(dtVisitanteOpts);
    } else {
        $('#fecha-programada-wrapper').hide();
        $('#fecha-programada-display').val('');
        $('#partido-club_local_id').val('');
        $('#club-local-display').val('');
        $('#partido-club_visitante_id').val('');
        $('#club-visitante-display').val('');
        var emptyOpt = '<option value="">-- Sin DT --</option>';
        $('#dt1-local, #dt2-local, #dt1-visitante, #dt2-visitante').html(emptyOpt);
    }
});
JS;
$this->registerJs($js);
?>
