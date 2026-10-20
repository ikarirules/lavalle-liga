<?php

use common\models\ListaJugadores;
use yii\helpers\Html;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var common\models\Club              $club */
/** @var common\models\ListaJugadores[]  $plantel */
/** @var common\models\Jugador[]         $disponibles */
/** @var array                           $optsRemera */

$this->title = 'Plantel (Buena Fe) — ' . $club->nombre;
$this->params['breadcrumbs'][] = ['label' => 'Listas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Construir el <select> de remera reutilizable (mismo patrón que lista-partido.php)
function buildSelectPlantel(string $id, ?int $valorActual, array $opts): string {
    $html = "<select class=\"remera-select form-control form-control-sm\" data-id=\"{$id}\">";
    foreach ($opts as $val => $label) {
        $selected = ((string)$val === (string)$valorActual) ? ' selected' : '';
        $html .= "<option value=\"{$val}\"{$selected}>{$label}</option>";
    }
    $html .= '</select>';
    return $html;
}
?>

<div class="lista-jugadores-plantel">

    <h2><?= Html::encode($this->title) ?></h2>
    <p class="text-muted">
        Plantel vigente del club: se carga una sola vez y se hereda automáticamente
        (jugador + remera) al poblar la lista de cada partido, en las categorías con Buena Fe habilitada.
        Solo hace falta tocarlo cuando hay una baja o un pase.
    </p>

    <p>
        <?= Html::a('← Club', ['/club/view', 'id' => $club->id], ['class' => 'btn btn-sm btn-default']) ?>
    </p>

    <div id="ajax-msg-plantel" style="display:none" class="alert alert-danger">Error al guardar la remera.</div>

    <table class="table table-bordered table-condensed">
        <thead>
            <tr class="table-active">
                <th>Jugador</th>
                <th>Categoría</th>
                <th style="width:90px">Remera</th>
                <th style="width:60px"></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($plantel)): ?>
            <tr>
                <td colspan="4" class="text-center text-muted">Sin jugadores en el plantel todavía.</td>
            </tr>
            <?php endif; ?>
            <?php foreach ($plantel as $entry):
                $jugador = $entry->jugador;
                if (!$jugador) continue;
            ?>
            <tr>
                <td>
                    <?= Html::encode($jugador->nombre) ?>
                    <?php if ($jugador->suspendido): ?>
                        <span class="badge badge-danger ms-1">suspendido</span>
                    <?php endif; ?>
                </td>
                <td><?= $jugador->categoria ? Html::encode($jugador->categoria->nombre) : '—' ?></td>
                <td><?= buildSelectPlantel((string)$entry->id, $entry->remera, $optsRemera) ?></td>
                <td class="text-center">
                    <?= Html::a('✕', ['delete', 'id' => $entry->id], [
                        'class' => 'text-danger',
                        'title' => 'Quitar del plantel',
                        'data'  => ['confirm' => '¿Quitar a ' . Html::encode($jugador->nombre) . ' del plantel?', 'method' => 'post'],
                    ]) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Agregar jugador al plantel</h3>
    <?= Html::beginForm(['plantel-agregar'], 'post', ['class' => 'form-inline']) ?>
        <?= Html::hiddenInput('club_id', $club->id) ?>
        <?= Html::dropDownList('jugador_id', null, array_reduce($disponibles, function ($carry, $j) {
            $cat = $j->categoria ? ' [' . $j->categoria->nombre . ']' : '';
            $carry[$j->id] = $j->nombre . ' — DNI ' . $j->dni . $cat;
            return $carry;
        }, []), [
            'prompt' => empty($disponibles) ? '-- No hay jugadores disponibles --' : '-- Elegir jugador --',
            'class'  => 'form-control mr-2',
            'style'  => 'min-width:320px',
        ]) ?>
        <?= Html::submitButton('+ Agregar', [
            'class'    => 'btn btn-sm btn-primary',
            'disabled' => empty($disponibles),
        ]) ?>
    <?= Html::endForm() ?>

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
                $('#ajax-msg-plantel').text('Error: ' + (r.error || 'desconocido')).show();
            } else {
                $('#ajax-msg-plantel').hide();
            }
        },
        error: function () {
            $('#ajax-msg-plantel').text('Error de conexión al guardar la remera.').show();
        },
        complete: function () {
            select.prop('disabled', false);
        }
    });
});
JS;
$this->registerJs($js);
?>
