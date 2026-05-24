<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Partidos $model */
/** @var array $fechasOptions */
/** @var array $fechasData */
/** @var bool $isArbitro */
/** @var array $arbitros */

$this->title = 'Editar Partido: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Partidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Editar';
?>
<div class="partidos-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model'             => $model,
        'fechasOptions'     => $fechasOptions,
        'fechasData'        => $fechasData,
        'isArbitro'         => $isArbitro,
        'arbitros'          => $arbitros,
        'categorias'        => $categorias,
        'directivosPorClub' => $directivosPorClub,
    ]) ?>

</div>
