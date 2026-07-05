<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/** @var $this yii\web\View */
/** @var $searchModel frontend\models\MultaSearch */
/** @var $dataProvider yii\data\ActiveDataProvider */
/** @var $clubs common\models\Club[] */
/** @var $jugadores common\models\Jugador[] */

$this->title = 'Multas';
?>
<div class="multa-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['index']]); ?>
    <div class="row g-2 mb-3">
        <div class="col-md-3">
            <?= Html::dropDownList('MultaSearch[club_id]', $searchModel->club_id,
                ArrayHelper::map($clubs, 'id', 'nombre'),
                ['class' => 'form-select', 'prompt' => '— Todos los clubes —']) ?>
        </div>
        <div class="col-md-3">
            <?= Html::textInput('MultaSearch[jugador_nombre]', $searchModel->jugador_nombre,
                ['class' => 'form-control', 'placeholder' => 'Nombre jugador...']) ?>
        </div>
        <div class="col-md-2">
            <?= Html::dropDownList('MultaSearch[pagado]', $searchModel->pagado,
                [0 => 'Pendientes', 1 => 'Pagadas'],
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
                'value'  => fn($m) => $m->jugador ? $m->jugador->nombre : '—',
            ],
            [
                'label'  => 'Club',
                'value'  => fn($m) => ($m->jugador && $m->jugador->club) ? $m->jugador->club->nombre : '—',
            ],
            [
                'label'  => 'Infracción',
                'value'  => fn($m) => ($m->informeDetalle && $m->informeDetalle->tipoInfraccion)
                    ? $m->informeDetalle->tipoInfraccion->nombre : '—',
            ],
            [
                'attribute' => 'monto',
                'value'     => fn($m) => '$ ' . number_format($m->monto, 2, ',', '.'),
            ],
            [
                'label'  => 'Reincidencia',
                'format' => 'raw',
                'value'  => function ($m) {
                    $n = \common\models\Multa::find()
                        ->where(['jugador_id' => $m->jugador_id])
                        ->andWhere(['<>', 'id', $m->id])
                        ->count();
                    return $n > 0
                        ? Html::tag('span', "⚠ {$n} prev.", ['class' => 'badge bg-warning text-dark'])
                        : Html::tag('span', 'Primera', ['class' => 'badge bg-success']);
                },
            ],
            [
                'label'  => 'Estado',
                'format' => 'raw',
                'value'  => fn($m) => $m->pagado
                    ? Html::tag('span', 'Pagada', ['class' => 'badge bg-success'])
                    : Html::tag('span', 'Pendiente', ['class' => 'badge bg-danger']),
            ],
            [
                'attribute' => 'fecha_pago',
                'value'     => fn($m) => $m->fecha_pago ? date('d/m/Y', strtotime($m->fecha_pago)) : '—',
            ],
            [
                'label'   => 'Acciones',
                'format'  => 'raw',
                'value'   => function ($m) {
                    $btns = Html::a('Ver', ['view', 'id' => $m->id], ['class' => 'btn btn-sm btn-outline-secondary']);
                    if (!$m->pagado) {
                        $btns .= ' ' . Html::beginForm(['marcar-pagada', 'id' => $m->id], 'post', ['style' => 'display:inline'])
                            . Html::submitButton('Marcar pagada', [
                                'class'   => 'btn btn-sm btn-success',
                                'onclick' => "return confirm('¿Confirmar pago de la multa?')",
                            ])
                            . Html::endForm();
                    } else {
                        $btns .= ' ' . Html::beginForm(['desmarcar-pagada', 'id' => $m->id], 'post', ['style' => 'display:inline'])
                            . Html::submitButton('Revertir', [
                                'class'   => 'btn btn-sm btn-outline-warning',
                                'onclick' => "return confirm('¿Revertir pago?')",
                            ])
                            . Html::endForm();
                    }
                    return $btns;
                },
            ],
        ],
    ]); ?>
</div>
