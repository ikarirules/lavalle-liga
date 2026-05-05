<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Partidos $model */
/** @var array $fechasOptions */
/** @var array $fechasData */
/** @var bool $isArbitro */
/** @var array $arbitros */

$this->title = 'Crear Partido';
$this->params['breadcrumbs'][] = ['label' => 'Partidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partidos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model'         => $model,
        'fechasOptions' => $fechasOptions,
        'fechasData'    => $fechasData,
        'isArbitro'     => $isArbitro,
        'arbitros'      => $arbitros,
        'categorias'    => $categorias,
    ]) ?>

</div>
