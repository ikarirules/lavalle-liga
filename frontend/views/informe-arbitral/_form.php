<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\InformeArbitral $model */
/** @var yii\widgets\ActiveForm $form */
/** @var array $partidosOptions  [grupoLabel => [partido_id => descripción]] */
/** @var bool $isArbitro */
/** @var array $tiposOptions  [id => nombre] */
/** @var array $arbitrosOptions  [id => username] */

// Preparar tipos para JS
$tiposJs = [];
foreach ($tiposOptions as $id => $nombre) {
    $tiposJs[] = ['id' => $id, 'nombre' => $nombre];
}
$ajaxUrl  = Url::to(['informe-arbitral/jugadores-por-partido']);

// Goles e infracciones ya cargados (edición): se prellenan una vez que se conocen los equipos del partido
$golesJs = [];
foreach ($model->goles as $gol) {
    $golesJs[] = ['club_id' => $gol->club_id, 'jugador_id' => $gol->jugador_id, 'cantidad' => $gol->cantidad];
}
$detallesJs = [];
foreach ($model->detalles as $det) {
    $detallesJs[] = [
        'club_id'            => $det->club_id,
        'jugador_id'         => $det->jugador_id,
        'tipo_infraccion_id' => $det->tipo_infraccion_id,
        'minuto'             => $det->minuto,
    ];
}
?>

<div class="informe-arbitral-form">

    <?php $form = ActiveForm::begin(); ?>

    <!-- CABECERA: 3 columnas responsive -->
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'partido_id')->dropDownList($partidosOptions, ['prompt' => 'Seleccionar partido...', 'id' => 'partido-selector']) ?>
        </div>
        <div class="col-md-4">
            <?php if ($isArbitro): ?>
                <?= $form->field($model, 'arbitro_id')->hiddenInput()->label(false) ?>
                <div class="form-group">
                    <label class="control-label"><?= $model->getAttributeLabel('arbitro_id') ?></label>
                    <p class="form-control-static"><?= Html::encode(\Yii::$app->user->identity->username) ?></p>
                </div>
            <?php else: ?>
                <?= $form->field($model, 'arbitro_id')->dropDownList($arbitrosOptions, ['prompt' => 'Seleccionar árbitro...']) ?>
            <?php endif; ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'asistente_id')->dropDownList($arbitrosOptions, ['prompt' => 'Seleccionar asistente...']) ?>
        </div>
    </div>

    <?= $form->field($model, 'observaciones')->textarea(['rows' => 4]) ?>

    <hr>

    <!-- GOLES -->
    <h4>Goles</h4>
    <p class="text-muted small">Seleccioná primero el partido para cargar los jugadores.</p>

    <table class="table table-bordered table-condensed" id="tabla-goles">
        <thead>
            <tr>
                <th>Equipo</th>
                <th>Jugador</th>
                <th style="width:100px">Cantidad</th>
                <th style="width:40px"></th>
            </tr>
        </thead>
        <tbody id="goles-body"></tbody>
    </table>
    <button type="button" class="btn btn-success btn-sm" id="btn-add-gol" disabled>
        + Agregar gol
    </button>

    <hr>

    <!-- INFRACCIONES -->
    <h4>Infracciones</h4>

    <table class="table table-bordered table-condensed" id="tabla-infracciones">
        <thead>
            <tr>
                <th>Equipo</th>
                <th>Jugador</th>
                <th>Tipo de infracción</th>
                <th style="width:90px">Minuto</th>
                <th style="width:40px"></th>
            </tr>
        </thead>
        <tbody id="infracciones-body"></tbody>
    </table>
    <button type="button" class="btn btn-warning btn-sm" id="btn-add-infraccion" disabled>
        + Agregar infracción
    </button>

    <div class="form-group" style="margin-top:24px">
        <?= Html::submitButton('Guardar Informe', ['class' => 'btn btn-success', 'id' => 'btn-guardar-informe']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$tiposJson    = json_encode($tiposJs, JSON_UNESCAPED_UNICODE);
$ajaxUrlJs    = json_encode($ajaxUrl);
$partidoInicial = json_encode($model->partido_id);
$golesJson    = json_encode($golesJs, JSON_UNESCAPED_UNICODE);
$detallesJson = json_encode($detallesJs, JSON_UNESCAPED_UNICODE);

$js = <<<JS
var equiposData = {};
var golIdx = 0;
var detIdx = 0;
var ajaxBase = $ajaxUrlJs;
var tiposInfraccion = $tiposJson;
var golesExistentes = $golesJson;
var detallesExistentes = $detallesJson;

function ajaxUrl(id) {
    var sep = ajaxBase.indexOf('?') !== -1 ? '&' : '?';
    return ajaxBase + sep + 'id=' + id;
}

function buildEquipoOpts() {
    var html = '<option value="">Equipo...</option>';
    if (equiposData.local)     html += '<option value="' + equiposData.local.club_id     + '">' + equiposData.local.nombre     + ' (Local)</option>';
    if (equiposData.visitante) html += '<option value="' + equiposData.visitante.club_id + '">' + equiposData.visitante.nombre + ' (Visitante)</option>';
    return html;
}

function buildJugadorOpts(clubId) {
    var html = '<option value="">Jugador...</option>';
    var equipo = null;
    if (equiposData.local     && equiposData.local.club_id     == clubId) equipo = equiposData.local;
    if (equiposData.visitante && equiposData.visitante.club_id == clubId) equipo = equiposData.visitante;
    if (equipo) {
        equipo.jugadores.forEach(function(j) {
            var label = j.nombre + (j.remera ? ' (#' + j.remera + ')' : '');
            html += '<option value="' + j.id + '">' + label + '</option>';
        });
    }
    return html;
}

function addGolRow(prefill) {
    var idx = golIdx++;
    var row = '<tr>' +
        '<td><select name="InformeGol[' + idx + '][club_id]" class="form-control equipo-sel" data-prefix="gol-jug-" data-idx="' + idx + '">' + buildEquipoOpts() + '</select></td>' +
        '<td><select name="InformeGol[' + idx + '][jugador_id]" id="gol-jug-' + idx + '" class="form-control"><option value="">Seleccioná equipo primero</option></select></td>' +
        '<td><input type="number" name="InformeGol[' + idx + '][cantidad]" class="form-control" value="' + (prefill ? prefill.cantidad : 1) + '" min="1" max="20"></td>' +
        '<td><button type="button" class="btn btn-danger btn-xs remove-row">&times;</button></td>' +
    '</tr>';
    var \$row = $(row).appendTo('#goles-body');
    if (prefill) {
        \$row.find('.equipo-sel').val(prefill.club_id);
        \$row.find('#gol-jug-' + idx).html(buildJugadorOpts(prefill.club_id)).val(prefill.jugador_id);
    }
}

function addInfraccionRow(prefill) {
    var idx = detIdx++;
    var tiposOpts = '<option value="">Tipo...</option>';
    tiposInfraccion.forEach(function(t) {
        tiposOpts += '<option value="' + t.id + '">' + t.nombre + '</option>';
    });
    var row = '<tr>' +
        '<td><select name="InformeDetalle[' + idx + '][club_id]" class="form-control equipo-sel" data-prefix="det-jug-" data-idx="' + idx + '">' + buildEquipoOpts() + '</select></td>' +
        '<td><select name="InformeDetalle[' + idx + '][jugador_id]" id="det-jug-' + idx + '" class="form-control"><option value="">Seleccioná equipo primero</option></select></td>' +
        '<td><select name="InformeDetalle[' + idx + '][tipo_infraccion_id]" class="form-control">' + tiposOpts + '</select></td>' +
        '<td><input type="number" name="InformeDetalle[' + idx + '][minuto]" class="form-control" min="1" max="120" placeholder="min" value="' + (prefill && prefill.minuto ? prefill.minuto : '') + '"></td>' +
        '<td><button type="button" class="btn btn-danger btn-xs remove-row">&times;</button></td>' +
    '</tr>';
    var \$row = $(row).appendTo('#infracciones-body');
    if (prefill) {
        \$row.find('.equipo-sel').val(prefill.club_id);
        \$row.find('#det-jug-' + idx).html(buildJugadorOpts(prefill.club_id)).val(prefill.jugador_id || '');
        \$row.find('select[name$="[tipo_infraccion_id]"]').val(prefill.tipo_infraccion_id);
    }
}

function cargarPartido(id, prefillGoles, prefillDetalles) {
    equiposData = {};
    $('#goles-body, #infracciones-body').empty();
    golIdx = 0; detIdx = 0;
    $('#btn-add-gol, #btn-add-infraccion').prop('disabled', true);
    if (!id) return;
    $.getJSON(ajaxUrl(id), function(data) {
        equiposData = data;
        $('#btn-add-gol, #btn-add-infraccion').prop('disabled', false);
        (prefillGoles || []).forEach(function(g) { addGolRow(g); });
        (prefillDetalles || []).forEach(function(d) { addInfraccionRow(d); });
    });
}

$('#partido-selector').on('change', function() {
    cargarPartido($(this).val());
});

$('#btn-add-gol').on('click', function() { addGolRow(null); });
$('#btn-add-infraccion').on('click', function() { addInfraccionRow(null); });

$(document).on('change', '.equipo-sel', function() {
    var prefix = $(this).data('prefix');
    var idx    = $(this).data('idx');
    var clubId = $(this).val();
    $('#' + prefix + idx).html(buildJugadorOpts(clubId));
});

$(document).on('click', '.remove-row', function() {
    $(this).closest('tr').remove();
});

var partidoInicial = $partidoInicial;
if (partidoInicial) {
    cargarPartido(partidoInicial, golesExistentes, detallesExistentes);
}

$('.informe-arbitral-form form').on('beforeSubmit', function() {
    var \$btn = $('#btn-guardar-informe');
    if (\$btn.prop('disabled')) {
        return false;
    }
    \$btn.prop('disabled', true).text('Guardando...');
    return true;
});
JS;

$this->registerJs($js);
?>
