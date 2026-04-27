<?php

/** @var \yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="alternate icon" href="/favicon.ico">
    <?php $this->head() ?>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<header>
    <?php
    $user = Yii::$app->user;

    NavBar::begin([
        'brandLabel' => '⚽ Liga',
        'brandUrl'   => Yii::$app->homeUrl,
        'options'    => [
            'class' => 'navbar navbar-expand-xl navbar-dark bg-dark fixed-top',
        ],
    ]);

    $menuItems = [];

    if (!$user->isGuest) {

        // --- COMPETENCIA (todos los usuarios autenticados) ---
        $menuItems[] = ['label' => 'Torneos',   'url' => ['/torneo/index']];
        $menuItems[] = ['label' => 'Fechas',    'url' => ['/fechas/index']];
        $menuItems[] = ['label' => 'Partidos',  'url' => ['/partidos/index']];
        $menuItems[] = ['label' => 'Clubes',    'url' => ['/club/index']];
        $menuItems[] = ['label' => 'Jugadores', 'url' => ['/jugador/index']];

        if ($user->can('arbitro') || $user->can('miembro_liga') || $user->can('admin_liga')) {
            if ($user->can('arbitro') || $user->can('admin_liga')) {
                $menuItems[] = ['label' => 'Nuevo Inf.', 'url' => ['/informe-arbitral/create']];
            }
            $menuItems[] = ['label' => 'Informes', 'url' => ['/informe-arbitral/index']];
        }

        if ($user->can('miembro_liga') || $user->can('admin_liga')) {
            $menuItems[] = ['label' => 'Categorías', 'url' => ['/categoria/index']];
        }
        if ($user->can('admin_liga')) {
            $menuItems[] = ['label' => 'Infracciones', 'url' => ['/tipo-infraccion/index']];
        }
    }

    echo Nav::widget([
        'options'         => ['class' => 'navbar-nav me-auto mb-2 mb-lg-0'],
        'items'           => $menuItems,
        'activateParents' => true,
    ]);

    // --- LOGIN / LOGOUT ---
    if ($user->isGuest) {
        echo Html::tag(
            'div',
            Html::a('Registrarse', ['/site/signup'], ['class' => 'btn btn-outline-light me-2'])
            . Html::a('Iniciar sesión', ['/site/login'], ['class' => 'btn btn-success']),
            ['class' => 'd-flex gap-2']
        );
    } else {
        $identity = $user->identity;
        echo Html::tag(
            'div',
            Html::tag('span', '👤 ' . Html::encode($identity->username), ['class' => 'navbar-text me-3 text-white'])
            . Html::beginForm(['/site/logout'], 'post')
            . Html::submitButton('Cerrar sesión', ['class' => 'btn btn-outline-light btn-sm'])
            . Html::endForm(),
            ['class' => 'd-flex align-items-center']
        );
    }

    NavBar::end();
    ?>
</header>

<main role="main" class="flex-shrink-0">
    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<footer class="footer mt-auto py-3 bg-dark text-muted">
    <div class="container d-flex justify-content-between">
        <span class="text-white-50">&copy; <?= date('Y') ?> Liga</span>
        <span class="text-white-50"><?= Yii::powered() ?></span>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
