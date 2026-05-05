<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\ListaJugadores $model */
/** @var array  $partidos       [id => label] */
/** @var array  $clubes         [id => nombre] */
/** @var string $jugadoresJson  JSON {club_id: [{id, text}, ...]} */
?>

<div class="lista-jugadores-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'tipo_lista')->dropDownList(
        \common\models\ListaJugadores::optsTipoLista(),
        ['id' => 'listajugadores-tipo_lista']
    )->label('Tipo de Lista') ?>

    <div id="campo-partido" style="<?= $model->tipo_lista === 'partido' ? '' : 'display:none' ?>">
        <?= $form->field($model, 'partido_id')->dropDownList($partidos, [
            'prompt' => '-- Seleccionar partido --',
            'id'     => 'listajugadores-partido_id',
        ])->label('Partido') ?>
    </div>

    <?= $form->field($model, 'club_id')->dropDownList($clubes, [
        'prompt' => '-- Seleccionar club --',
        'id'     => 'listajugadores-club_id',
    ])->label('Club') ?>

    <?= $form->field($model, 'jugador_id')->dropDownList([], [
        'prompt' => '-- Seleccionar club primero --',
        'id'     => 'listajugadores-jugador_id',
    ])->label('Jugador') ?>

    <?= $form->field($model, 'remera_local')->textInput(['type' => 'number', 'min' => 1, 'max' => 99])->label('Remera Local') ?>

    <?= $form->field($model, 'remera_visitante')->textInput(['type' => 'number', 'min' => 1, 'max' => 99])->label('Remera Visitante') ?>

    <div class="form-group">
        <?= Html::submitButton('Guardar', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$currentClub    = (int) $model->club_id;
$currentJugador = (int) $model->jugador_id;
$js = <<<JS
var jugadoresPorClub = $jugadoresJson;

// Mostrar / ocultar campo partido según tipo de lista
$('#listajugadores-tipo_lista').on('change', function () {
    if ($(this).val() === 'partido') {
        $('#campo-partido').show();
    } else {
        $('#campo-partido').hide();
    }
});

// Rellenar dropdown de jugadores al cambiar club
$('#listajugadores-club_id').on('change', function () {
    var clubId = $(this).val();
    var sel    = $('#listajugadores-jugador_id');
    sel.empty().append('<option value="">-- Seleccionar jugador --</option>');
    if (clubId && jugadoresPorClub[clubId]) {
        $.each(jugadoresPorClub[clubId], function (i, j) {
            sel.append('<option value="' + j.id + '">' + j.text + '</option>');
        });
    }
    sel.val('');
});

// Si ya hay club seleccionado (edición / repost), poblar jugadores y restaurar selección
(function () {
    var clubId  = $currentClub;
    var jugId   = $currentJugador;
    if (!clubId) return;
    var sel = $('#listajugadores-jugador_id');
    sel.empty().append('<option value="">-- Seleccionar jugador --</option>');
    if (jugadoresPorClub[clubId]) {
        $.each(jugadoresPorClub[clubId], function (i, j) {
            sel.append('<option value="' + j.id + '">' + j.text + '</option>');
        });
    }
    if (jugId) sel.val(jugId);
})();
JS;
$this->registerJs($js);
?>
