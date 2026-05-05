<?php

use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\Partidos $model */
/** @var yii\widgets\ActiveForm $form */
/** @var array $fechasOptions   [id => "Fecha #N — dd/mm/yyyy"] */
/** @var array $fechasData      [id => [fecha_programada, club_local_id, ...]] */
/** @var bool  $isArbitro       true si el usuario logueado tiene rol árbitro */
/** @var array $arbitros        [username => username] — vacío si $isArbitro */
/** @var array $categorias      [nombre => nombre] */

// Determinar valores de display para club local/visitante al cargar (update / repost con error)
$currentFechaData = ($model->fecha_id && isset($fechasData[$model->fecha_id]))
    ? $fechasData[$model->fecha_id]
    : null;
?>

<div class="partidos-form">

    <?php $form = ActiveForm::begin(); ?>

    <!-- Fecha -->
    <?= $form->field($model, 'fecha_id')->dropDownList($fechasOptions, [
        'prompt' => '-- Seleccionar fecha --',
        'id'     => 'partido-fecha_id',
    ])->label('Fecha') ?>

    <!-- Fecha programada (solo lectura, se llena con JS) -->
    <div class="form-group" id="fecha-programada-wrapper"<?= $currentFechaData ? '' : ' style="display:none"' ?>>
        <label class="control-label">Fecha Programada</label>
        <input type="text" id="fecha-programada-display" class="form-control" readonly
               value="<?= $currentFechaData ? Html::encode($currentFechaData['fecha_programada']) : '' ?>">
    </div>

    <!-- Categoría -->
    <?= $form->field($model, 'categoria')->dropDownList($categorias, ['prompt' => '-- Seleccionar categoría --']) ?>

    <!-- Club Local — hidden input (valor real) + campo visible de solo lectura -->
    <?= $form->field($model, 'club_local_id')->hiddenInput(['id' => 'partido-club_local_id'])->label(false) ?>
    <div class="form-group">
        <label class="control-label">Club Local</label>
        <input type="text" id="club-local-display" class="form-control" readonly
               value="<?= $currentFechaData ? Html::encode($currentFechaData['club_local_nombre']) : '' ?>"
               placeholder="Se completa al seleccionar la fecha">
    </div>

    <!-- Club Visitante — hidden input (valor real) + campo visible de solo lectura -->
    <?= $form->field($model, 'club_visitante_id')->hiddenInput(['id' => 'partido-club_visitante_id'])->label(false) ?>
    <div class="form-group">
        <label class="control-label">Club Visitante</label>
        <input type="text" id="club-visitante-display" class="form-control" readonly
               value="<?= $currentFechaData ? Html::encode($currentFechaData['club_visitante_nombre']) : '' ?>"
               placeholder="Se completa al seleccionar la fecha">
    </div>

    <!-- Cancha -->
    <?= $form->field($model, 'cancha')->textInput(['maxlength' => true]) ?>

    <!-- Estado -->
    <?= $form->field($model, 'estado')->dropDownList([
        'programada' => 'Programada',
        'suspendida' => 'Suspendida',
        'postergada' => 'Postergada',
        'jugada'     => 'Jugada',
    ]) ?>

    <!-- Árbitro: solo lectura si el logueado es árbitro, desplegable si no -->
    <?php if ($isArbitro): ?>
        <?= $form->field($model, 'arbitro')->textInput(['readonly' => true])->label('Árbitro') ?>
    <?php else: ?>
        <?= $form->field($model, 'arbitro')->dropDownList($arbitros, [
            'prompt' => '-- Seleccionar árbitro --',
        ])->label('Árbitro') ?>
    <?php endif; ?>

    <?= $form->field($model, 'asistente1')->textInput(['maxlength' => true])->label('Asistente 1') ?>
    <?= $form->field($model, 'asistente2')->textInput(['maxlength' => true])->label('Asistente 2') ?>
    <?= $form->field($model, 'asistente3')->textInput(['maxlength' => true])->label('Asistente 3') ?>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$fechasJson = Json::encode($fechasData);
$js = <<<JS
var fechasData = $fechasJson;

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
    } else {
        $('#fecha-programada-wrapper').hide();
        $('#fecha-programada-display').val('');
        $('#partido-club_local_id').val('');
        $('#club-local-display').val('');
        $('#partido-club_visitante_id').val('');
        $('#club-visitante-display').val('');
    }
});
JS;
$this->registerJs($js);
?>
