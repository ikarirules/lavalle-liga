<?php

use common\models\ListaJugadores;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Partidos         $partido */
/** @var common\models\ListaJugadores[] $locales */
/** @var common\models\ListaJugadores[] $visitantes */
/** @var common\models\Jugador[]        $jugadoresLocales */
/** @var common\models\Jugador[]        $jugadoresVisitantes */
/** @var array                          $optsRemera */

$localNombre     = $partido->clubLocal     ? $partido->clubLocal->nombre     : 'Local';
$visitanteNombre = $partido->clubVisitante ? $partido->clubVisitante->nombre : 'Visitante';

$numeroFecha = $partido->fecha ? 'Fecha #' . $partido->fecha->numero_fecha : '';
$this->title = "Lista del Partido #{$partido->id}" . ($numeroFecha ? " — {$numeroFecha}" : '');
$this->params['breadcrumbs'][] = ['label' => 'Listas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$totalFilas = max(count($locales), count($visitantes), 1);

// Construir el <select> de remera reutilizable
function buildSelect(string $id, ?int $valorActual, array $opts): string {
    $html = "<select class=\"remera-select form-control form-control-sm\" data-id=\"{$id}\">";
    foreach ($opts as $val => $label) {
        $selected = ((string)$val === (string)$valorActual) ? ' selected' : '';
        $html .= "<option value=\"{$val}\"{$selected}>{$label}</option>";
    }
    $html .= '</select>';
    return $html;
}
?>

<div class="lista-partido-view">

    <h2><?= Html::encode($this->title) ?></h2>
    <p class="text-muted">
        <?php if ($partido->fecha): ?>
            <strong>Fecha #<?= $partido->fecha->numero_fecha ?></strong> &mdash;
        <?php endif; ?>
        Categoría: <strong><?= Html::encode($partido->categoria) ?></strong>
        &mdash;
        <?= Html::encode($localNombre) ?> <strong>vs</strong> <?= Html::encode($visitanteNombre) ?>
    </p>

    <?php if ($partido->arbitro || $partido->dt1_local || $partido->dt1_visitante): ?>
    <div class="row mb-3">
        <div class="col-sm-4">
            <div class="card card-body py-2 px-3 bg-light">
                <small class="text-muted d-block mb-1">Cuerpo técnico local</small>
                <?php if ($partido->dt1_local): ?>
                    <span><strong>DT 1:</strong> <?= Html::encode($partido->dt1_local) ?></span><br>
                <?php endif; ?>
                <?php if ($partido->dt2_local): ?>
                    <span><strong>DT 2:</strong> <?= Html::encode($partido->dt2_local) ?></span>
                <?php endif; ?>
                <?php if (!$partido->dt1_local && !$partido->dt2_local): ?>
                    <span class="text-muted">—</span>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-sm-4 text-center d-flex align-items-center justify-content-center">
            <?php if ($partido->arbitro): ?>
                <div>
                    <small class="text-muted d-block">Árbitro</small>
                    <strong><?= Html::encode($partido->arbitro) ?></strong>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-sm-4">
            <div class="card card-body py-2 px-3 bg-light">
                <small class="text-muted d-block mb-1">Cuerpo técnico visitante</small>
                <?php if ($partido->dt1_visitante): ?>
                    <span><strong>DT 1:</strong> <?= Html::encode($partido->dt1_visitante) ?></span><br>
                <?php endif; ?>
                <?php if ($partido->dt2_visitante): ?>
                    <span><strong>DT 2:</strong> <?= Html::encode($partido->dt2_visitante) ?></span>
                <?php endif; ?>
                <?php if (!$partido->dt1_visitante && !$partido->dt2_visitante): ?>
                    <span class="text-muted">—</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <p>
        <?= Html::beginForm(['poblar-lista'], 'post', ['style' => 'display:inline']) ?>
            <?= Html::hiddenInput('partido_id', $partido->id) ?>
            <?= Html::submitButton('⟳ Cargar jugadores del plantel', [
                'class' => 'btn btn-sm btn-success',
                'data'  => ['confirm' => '¿Cargar automáticamente los jugadores de ambos clubes según la categoría del partido? No se duplicarán los que ya estén.'],
            ]) ?>
        <?= Html::endForm() ?>
        <?= Html::a('+ Local',
            ['create', 'partido_id' => $partido->id, 'lado' => 'local'],
            ['class' => 'btn btn-sm btn-primary']) ?>
        <?= Html::a('+ Visitante',
            ['create', 'partido_id' => $partido->id, 'lado' => 'visitante'],
            ['class' => 'btn btn-sm btn-warning']) ?>
        <?= Html::a('⬇ Descargar Excel',
            ['exportar-excel', 'partido_id' => $partido->id],
            ['class' => 'btn btn-sm btn-success']) ?>
        <?= Html::a('← Partido',
            ['/partidos/view', 'id' => $partido->id],
            ['class' => 'btn btn-sm btn-default']) ?>
    </p>

    <div id="ajax-msg" style="display:none" class="alert alert-danger">Error al guardar la remera.</div>

    <table class="table table-bordered table-condensed lista-partido-table">
        <thead>
            <tr>
                <th colspan="2" class="text-center bg-primary text-white">
                    <?= Html::encode($localNombre) ?> (Local)
                    <small class="text-white-50">(<?= count($locales) ?>)</small>
                </th>
                <th colspan="2" class="text-center bg-secondary text-white">
                    <?= Html::encode($visitanteNombre) ?> (Visitante)
                    <small class="text-white-50">(<?= count($visitantes) ?>)</small>
                </th>
                <th></th>
            </tr>
            <tr>
                <th>Jugador Local</th>
                <th style="width:90px">Remera</th>
                <th>Jugador Visitante</th>
                <th style="width:90px">Remera</th>
                <th style="width:60px"></th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < $totalFilas; $i++):
                $l = $locales[$i]    ?? null;
                $v = $visitantes[$i] ?? null;
            ?>
            <tr>
                <!-- Local -->
                <td><?= $l ? Html::encode($l->jugador ? $l->jugador->nombre : '—') : '' ?></td>
                <td>
                    <?php if ($l): ?>
                        <?= buildSelect((string)$l->id, $l->remera, $optsRemera) ?>
                    <?php endif; ?>
                </td>

                <!-- Visitante -->
                <td><?= $v ? Html::encode($v->jugador ? $v->jugador->nombre : '—') : '' ?></td>
                <td>
                    <?php if ($v): ?>
                        <?= buildSelect((string)$v->id, $v->remera, $optsRemera) ?>
                    <?php endif; ?>
                </td>

                <!-- Acciones de fila -->
                <td class="text-center">
                    <?php if ($l): ?>
                        <?= Html::a('✕', ['delete', 'id' => $l->id], [
                            'class' => 'text-danger',
                            'title' => 'Quitar local',
                            'data'  => ['confirm' => '¿Quitar jugador local?', 'method' => 'post'],
                        ]) ?>
                    <?php endif; ?>
                    <?php if ($v): ?>
                        <?= Html::a('✕', ['delete', 'id' => $v->id], [
                            'class' => 'text-danger ms-1',
                            'title' => 'Quitar visitante',
                            'data'  => ['confirm' => '¿Quitar jugador visitante?', 'method' => 'post'],
                        ]) ?>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endfor; ?>

            <?php if ($totalFilas === 0): ?>
            <tr>
                <td colspan="5" class="text-center text-muted">Sin jugadores cargados aún.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>

<?php
$urlUpdateRemera = Url::to(['update-remera']);
$csrfName  = Yii::$app->request->csrfParam;
$csrfToken = Yii::$app->request->csrfToken;

$js = <<<JS
$('.remera-select').on('change', function () {
    var id     = $(this).data('id');
    var remera = $(this).val();
    var select = $(this);

    select.prop('disabled', true);

    $.ajax({
        url:  '$urlUpdateRemera',
        type: 'POST',
        data: { id: id, remera: remera, '$csrfName': '$csrfToken' },
        dataType: 'json',
        success: function (r) {
            if (!r.success) {
                $('#ajax-msg').text('Error: ' + (r.error || 'desconocido')).show();
            } else {
                $('#ajax-msg').hide();
            }
        },
        error: function () {
            $('#ajax-msg').text('Error de conexión al guardar la remera.').show();
        },
        complete: function () {
            select.prop('disabled', false);
        }
    });
});
JS;
$this->registerJs($js);
?>
