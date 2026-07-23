<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/** @var $this yii\web\View */
/** @var $searchModel frontend\models\InfraccionSearch */
/** @var $dataProvider yii\data\ActiveDataProvider */
/** @var $clubs common\models\Club[] */
/** @var $jugadores common\models\Jugador[] */

$this->title = 'Infracciones';
?>
<div class="multa-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['index']]); ?>
    <div class="row g-2 mb-3">
        <div class="col-md-3">
            <?= Html::dropDownList('InfraccionSearch[club_id]', $searchModel->club_id,
                ArrayHelper::map($clubs, 'id', 'nombre'),
                ['class' => 'form-select', 'prompt' => '— Todos los clubes —']) ?>
        </div>
        <div class="col-md-3">
            <?= Html::textInput('InfraccionSearch[jugador_nombre]', $searchModel->jugador_nombre,
                ['class' => 'form-control', 'placeholder' => 'Nombre jugador...']) ?>
        </div>
        <div class="col-md-2">
            <?= Html::dropDownList('InfraccionSearch[pagado]', $searchModel->pagado,
                [0 => 'Multa pendiente', 1 => 'Multa pagada'],
                ['class' => 'form-select', 'prompt' => '— Todas —']) ?>
        </div>
        <div class="col-md-2">
            <?= Html::submitButton('Filtrar', ['class' => 'btn btn-secondary']) ?>
            <?= Html::a('Limpiar', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'label'  => 'Jugador',
                'value'  => fn($d) => $d->jugador ? $d->jugador->nombre : '—',
            ],
            [
                'label'  => 'Club',
                'value'  => fn($d) => ($d->jugador && $d->jugador->club) ? $d->jugador->club->nombre : '—',
            ],
            [
                'label'  => 'Infracción',
                'value'  => fn($d) => $d->tipoInfraccion ? $d->tipoInfraccion->nombre : '—',
            ],
            [
                'label'  => 'Multa',
                'value'  => fn($d) => $d->multa ? ('$ ' . number_format($d->multa->monto, 2, ',', '.')) : '—',
            ],
            [
                'label'  => 'Estado multa',
                'format' => 'raw',
                'value'  => function ($d) {
                    if (!$d->multa) {
                        return '<span class="text-muted">—</span>';
                    }
                    return $d->multa->pagado
                        ? Html::tag('span', 'Pagada', ['class' => 'badge bg-success'])
                        : Html::tag('span', 'Pendiente', ['class' => 'badge bg-danger']);
                },
            ],
            [
                'label'  => 'Inhabilitación',
                'format' => 'raw',
                'value'  => function ($d) {
                    if (!$d->tieneSancion()) {
                        return '<span class="text-muted">—</span>';
                    }
                    if ($d->sancion_levantada) {
                        return Html::tag('span', 'Levantada', ['class' => 'badge bg-secondary']);
                    }
                    return $d->sancionVigente
                        ? Html::tag('span', 'Vigente', ['class' => 'badge bg-danger'])
                        : Html::tag('span', 'Cumplida', ['class' => 'badge bg-success']);
                },
            ],
            [
                'label'   => 'Acciones',
                'format'  => 'raw',
                'value'   => function ($d) {
                    $btns = [];

                    if ($d->multa) {
                        $btns[] = Html::a('Ver multa', ['/multa/view', 'id' => $d->multa->id], ['class' => 'btn btn-sm btn-outline-secondary']);
                        if (!$d->multa->pagado) {
                            $btns[] = Html::beginForm(['/multa/marcar-pagada', 'id' => $d->multa->id], 'post', ['style' => 'display:inline'])
                                . Html::submitButton('Marcar pagada', [
                                    'class'   => 'btn btn-sm btn-success',
                                    'onclick' => "return confirm('¿Confirmar pago de la multa?')",
                                ])
                                . Html::endForm();
                        } else {
                            $btns[] = Html::beginForm(['/multa/desmarcar-pagada', 'id' => $d->multa->id], 'post', ['style' => 'display:inline'])
                                . Html::submitButton('Revertir pago', [
                                    'class'   => 'btn btn-sm btn-outline-warning',
                                    'onclick' => "return confirm('¿Revertir pago?')",
                                ])
                                . Html::endForm();
                        }
                    }

                    if ($d->tieneSancion()) {
                        if (!$d->sancion_levantada) {
                            $btns[] = Html::beginForm(['/informe-detalle/levantar-sancion', 'id' => $d->id], 'post', ['style' => 'display:inline'])
                                . Html::submitButton('Levantar inhabilitación', [
                                    'class'   => 'btn btn-sm btn-warning',
                                    'onclick' => "return confirm('¿Levantar la inhabilitación de este jugador?')",
                                ])
                                . Html::endForm();
                        } else {
                            $btns[] = Html::beginForm(['/informe-detalle/revertir-sancion', 'id' => $d->id], 'post', ['style' => 'display:inline'])
                                . Html::submitButton('Revertir', [
                                    'class'   => 'btn btn-sm btn-outline-danger',
                                    'onclick' => "return confirm('¿Restablecer la inhabilitación?')",
                                ])
                                . Html::endForm();
                        }
                    }

                    return implode(' ', $btns);
                },
            ],
        ],
    ]); ?>
</div>
