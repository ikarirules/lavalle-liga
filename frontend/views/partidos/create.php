<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Partidos $model */

$this->title = 'Create Partidos';
$this->params['breadcrumbs'][] = ['label' => 'Partidos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partidos-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
