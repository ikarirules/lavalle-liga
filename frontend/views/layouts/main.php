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
    <link rel="icon" type="image/svg+xml" href="<?= Yii::getAlias('@web') ?>/favicon.svg">
    <link rel="alternate icon" href="<?= Yii::getAlias('@web') ?>/favicon.ico">
    <?php $this->head() ?>
    <style>
        /* ── Fondo general ── */
        body { background-color: #2b2d31; color: #d0d0d0; padding-top: 70px; }
        main .container { padding-top: .4rem; }
        .breadcrumb { margin-bottom: .5rem; }

        /* ── Navbar neón ── */
        .navbar-brand {
            color: #39ff14 !important;
            text-shadow: 0 0 10px #39ff14;
            font-weight: 700;
        }
        .navbar-brand:hover { color: #00f5ff !important; text-shadow: 0 0 14px #00f5ff; }

        .navbar-nav .nav-link {
            color: #39ff14 !important;
            text-shadow: 0 0 6px #39ff1480;
            transition: color .2s, text-shadow .2s;
        }
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: #00f5ff !important;
            text-shadow: 0 0 10px #00f5ff;
        }

        /* toggler hamburguesa */
        .navbar-toggler { border-color: #39ff1460; }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%2339ff14' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* usuario logueado */
        .navbar-text { color: #39ff14 !important; text-shadow: 0 0 6px #39ff1480; }

        /* botones en navbar */
        .navbar .btn-outline-light {
            color: #39ff14;
            border-color: #39ff14;
            text-shadow: 0 0 6px #39ff1480;
        }
        .navbar .btn-outline-light:hover {
            background: #39ff14;
            color: #0a0a0a;
            box-shadow: 0 0 12px #39ff14;
        }
        .navbar .btn-success {
            background: #00f5ff;
            border-color: #00f5ff;
            color: #0a0a0a;
            text-shadow: none;
            box-shadow: 0 0 10px #00f5ffaa;
        }
        .navbar .btn-success:hover {
            background: #39ff14;
            border-color: #39ff14;
            box-shadow: 0 0 14px #39ff14;
        }

        /* ── Banners publicitarios (modo deslogueado) ── */
        .ad-banner {
            position: fixed;
            top: 80px;
            bottom: 60px;
            width: 110px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: .6rem;
            background: #1b1c1f;
            border: 1px dashed #39ff1460;
            color: #6c6f76;
            font-size: .85rem;
            z-index: 10;
        }
        .ad-banner-left  { left: 8px; }
        .ad-banner-right { right: 8px; }

        /* Angosta el contenido central para que no quede debajo de los banners */
        @media (min-width: 992px) {
            body.has-ad-banners main .container,
            body.has-ad-banners footer .container {
                max-width: calc(100% - 260px);
            }
        }

        /* ── Contenido de muestra del banner (sponsor ficticio) ── */
        .ad-banner-sponsor {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .4rem;
            padding: 0;
            color: #d0d0d0;
        }
        .ad-banner-sponsor .ad-logo { font-size: 1.6rem; }
        .ad-banner-sponsor .ad-brand {
            font-weight: 700;
            font-size: .8rem;
            line-height: 1.1;
            color: #39ff14;
            text-shadow: 0 0 6px #39ff1480;
        }
        .ad-banner-sponsor .ad-tag {
            font-size: .68rem;
            line-height: 1.1;
            color: #b0b0b0;
        }
        .ad-banner-sponsor .ad-offer {
            font-size: .68rem;
            line-height: 1.1;
            font-weight: 600;
            color: #00f5ff;
        }
        .ad-banner-sponsor .ad-cta {
            margin-top: .3rem;
            font-size: .65rem;
            padding: .25rem .5rem;
            border: 1px solid #39ff14;
            border-radius: 4px;
            color: #39ff14;
            text-decoration: none;
        }
        .ad-banner-sponsor .ad-cta:hover {
            background: #39ff14;
            color: #0a0a0a;
        }
        .ad-banner-sponsor .ad-disclaimer {
            font-size: .58rem;
            color: #555;
            margin-top: .4rem;
        }
    </style>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<?php if (false && Yii::$app->user->isGuest): // Publicidades desactivadas temporalmente ?>
    <div class="ad-banner ad-banner-left d-none d-lg-flex">
        <div class="ad-banner-sponsor">
            <span class="ad-logo">👟</span>
            <span class="ad-brand">Botinería Full Gol</span>
            <span class="ad-tag">Botines, pelotas y ropa deportiva</span>
            <span class="ad-offer">15% OFF para jugadores de la Liga</span>
            <a href="#" class="ad-cta">Ver ofertas</a>
            <span class="ad-disclaimer">Publicidad de muestra</span>
        </div>
    </div>
    <div class="ad-banner ad-banner-right d-none d-lg-flex">
        <div class="ad-banner-sponsor">
            <span class="ad-logo">🍕</span>
            <span class="ad-brand">Pizzería La Redonda</span>
            <span class="ad-tag">La previa y el post-partido</span>
            <span class="ad-offer">2x1 en pizzas los domingos</span>
            <a href="#" class="ad-cta">Pedir ahora</a>
            <span class="ad-disclaimer">Publicidad de muestra</span>
        </div>
    </div>
<?php endif; ?>

<header>
    <?php
    $user = Yii::$app->user;

    NavBar::begin([
        'brandLabel' => '⚽ Liga',
        'brandUrl'   => Yii::$app->homeUrl,
        'options'    => [
            'class' => 'navbar navbar-expand-xl fixed-top',
            'style' => 'background:#0a0a0a; border-bottom:1px solid #39ff1440;',
        ],
    ]);

    $menuItems = [];

    // --- Visible también en modo deslogueado ---
    $menuItems[] = ['label' => 'Jugadores', 'url' => ['/jugador/index']];

    if (!$user->isGuest) {

        // --- COMPETENCIA (todos los usuarios autenticados) ---
        $menuItems[] = ['label' => 'Torneos',   'url' => ['/torneo/index']];
        $menuItems[] = ['label' => 'Fechas',    'url' => ['/fechas/index']];
        $menuItems[] = ['label' => 'Partidos',  'url' => ['/partidos/index']];
        $menuItems[] = ['label' => 'Clubes',    'url' => ['/club/index']];

        if ($user->can('arbitro') || $user->can('directivo') || $user->can('miembro_liga') || $user->can('admin_liga')) {
            $menuItems[] = ['label' => 'Listas', 'url' => ['/lista-jugadores/index']];
        }

        if ($user->can('arbitro') || $user->can('miembro_liga') || $user->can('admin_liga')) {
            if ($user->can('arbitro') || $user->can('admin_liga')) {
                $menuItems[] = ['label' => 'Nuevo Inf.', 'url' => ['/informe-arbitral/create']];
            }
            $menuItems[] = ['label' => 'Informes', 'url' => ['/informe-arbitral/index']];
        }

        if ($user->can('directivo') || $user->can('miembro_liga') || $user->can('admin_liga')) {
            $menuItems[] = ['label' => 'Multas', 'url' => ['/multa/index']];
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
            Html::a('Iniciar sesión', ['/site/login'], ['class' => 'btn btn-success']),
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
