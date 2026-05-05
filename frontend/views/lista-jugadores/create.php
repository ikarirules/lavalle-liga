<?php

use common\models\ListaJugadores;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\ListaJugadores $model */
/** @var array  $partidos               [id => label] */
/** @var string $jugadoresPorPartido    JSON {partido_id: {local:[{id,text}], visitante:[...], local_club_id, visitante_club_id}} */

$this->title = 'Agregar Jugador a Lista';
$this->params['breadcrumbs'][] = ['label' => 'Listas', 'url' => ['index']];
if ($model->partido_id) {
    $this->params['breadcrumbs'][] = ['label' => "Lista Partido #{$model->partido_id}", 'url' => ['lista-partido', 'partido_id' => $model->partido_id]];
}
$this->params['breadcrumbs'][] = $this->title;

// Lado pre-seleccionado (local / visitante) que puede venir por GET
$ladoGet = Yii::$app->request->get('lado', '');
?>

<div class="lista-jugadores-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <!-- Partido -->
    <?= $form->field($model, 'partido_id')->dropDownList($partidos, [
        'prompt' => '-- Seleccionar partido --',
        'id'     => 'lj-partido',
    ])->label('Partido') ?>

    <!-- Lado (local / visitante) - controla qué club y jugadores se muestran -->
    <div class="form-group" id="campo-lado">
        <label class="control-label">Lado</label>
        <select id="lj-lado" name="lado" class="form-control">
            <option value="">-- Seleccionar lado --</option>
            <option value="local"     <?= $ladoGet === 'local'     ? 'selected' : '' ?>>Local</option>
            <option value="visitante" <?= $ladoGet === 'visitante' ? 'selected' : '' ?>>Visitante</option>
        </select>
    </div>

    <!-- Club (solo lectura, se llena por JS) -->
    <?= $form->field($model, 'club_id')->hiddenInput(['id' => 'lj-club-id'])->label(false) ?>
    <div class="form-group">
        <label class="control-label">Club</label>
        <input type="text" id="lj-club-nombre" class="form-control" readonly placeholder="Se completa al elegir partido y lado">
    </div>

    <!-- Jugador -->
    <?= $form->field($model, 'jugador_id')->dropDownList([], [
        'prompt' => '-- Elegir partido y lado primero --',
        'id'     => 'lj-jugador',
    ])->label('Jugador') ?>

    <!-- Remera -->
    <?= $form->field($model, 'remera')->dropDownList(
        ListaJugadores::optsRemera(),
        ['id' => 'lj-remera']
    )->label('Remera') ?>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
        <?php if ($model->partido_id): ?>
            <?= Html::a('Cancelar', ['lista-partido', 'partido_id' => $model->partido_id], ['class' => 'btn btn-default']) ?>
        <?php endif; ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$jpJson = $jugadoresPorPartido; // ya es JSON string
$js = <<<JS
var jpData = $jpJson;

function actualizarJugadores() {
    var pid  = $('#lj-partido').val();
    var lado = $('#lj-lado').val();
    var sel  = $('#lj-jugador');
    var clubNombre = $('#lj-club-nombre');
    var clubId     = $('#lj-club-id');

    sel.empty().append('<option value="">-- Seleccionar jugador --</option>');
    clubNombre.val('');
    clubId.val('');

    if (!pid || !lado || !jpData[pid]) return;

    var data = jpData[pid];
    var lista = (lado === 'local') ? data.local : data.visitante;
    var cid   = (lado === 'local') ? data.local_club_id : data.visitante_club_id;

    clubId.val(cid);
    // Buscar nombre del club en los jugadores (o simplemente indicar el id)
    // Mostramos cantidad de jugadores disponibles
    clubNombre.val('Club ID: ' + cid + ' (' + lista.length + ' jugadores disponibles)');

    $.each(lista, function(i, j) {
        sel.append('<option value="' + j.id + '">' + j.text + '</option>');
    });
    sel.val('');
}

$('#lj-partido, #lj-lado').on('change', actualizarJugadores);

// Inicializar si ya hay partido pre-seleccionado
(function() {
    var pid  = $('#lj-partido').val();
    var lado = '$ladoGet';
    if (pid && lado) {
        $('#lj-lado').val(lado);
        actualizarJugadores();
    }
})();
JS;
$this->registerJs($js);
?>
