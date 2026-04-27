<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Categoria[] $categorias */

$this->title = 'Categorías';
$this->params['breadcrumbs'][] = $this->title;

$puedeEditar = Yii::$app->user->can('miembro_liga') || Yii::$app->user->can('admin_liga');
?>
<div class="categoria-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>Categoría</th>
                <th class="text-center">Nacidos desde</th>
                <th class="text-center">Nacidos hasta</th>
                <th class="text-center">Permite Buena Fe</th>
                <th class="text-center">Activo</th>
                <?php if ($puedeEditar): ?>
                <th class="text-center">Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($categorias as $cat): ?>
            <tr>
                <td><?= Html::encode($cat->nombre) ?></td>
                <td class="text-center"><?= $cat->fecha_desde ? Yii::$app->formatter->asDate($cat->fecha_desde) : '-' ?></td>
                <td class="text-center"><?= $cat->fecha_hasta ? Yii::$app->formatter->asDate($cat->fecha_hasta) : '-' ?></td>
                <td class="text-center">
                    <?php if ($cat->permite_bf): ?>
                        <span class="badge badge-success">SI</span>
                    <?php else: ?>
                        <span class="badge badge-secondary">NO</span>
                    <?php endif; ?>
                </td>
                <td class="text-center">
                    <?= $cat->activo
                        ? '<span class="badge badge-success">Activo</span>'
                        : '<span class="badge badge-danger">Inactivo</span>' ?>
                </td>
                <?php if ($puedeEditar): ?>
                <td class="text-center">
                    <?= Html::a('Editar', ['update', 'id' => $cat->id], ['class' => 'btn btn-sm btn-primary']) ?>
                    <?= Html::beginForm(['toggle-bf', 'id' => $cat->id], 'post', ['style' => 'display:inline']) ?>
                    <?= Html::submitButton(
                        $cat->permite_bf ? 'Quitar BF' : 'Habilitar BF',
                        ['class' => 'btn btn-sm ' . ($cat->permite_bf ? 'btn-warning' : 'btn-success')]
                    ) ?>
                    <?= Html::endForm() ?>
                </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>
