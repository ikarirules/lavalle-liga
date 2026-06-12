<?php

use yii\helpers\Html;

/** @var \yii\web\View $this */
/** @var \yii\mail\MessageInterface $message */
/** @var string $content */

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        body { margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif; }
        .wrapper { width: 100%; background-color: #f4f4f4; padding: 30px 0; }
        .container { max-width: 580px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background-color: #1a3a5c; padding: 28px 30px; text-align: center; }
        .header h1 { margin: 0; color: #ffffff; font-size: 22px; letter-spacing: 1px; text-transform: uppercase; }
        .header p { margin: 4px 0 0; color: #a8c4e0; font-size: 13px; letter-spacing: 2px; text-transform: uppercase; }
        .accent-bar { height: 4px; background: linear-gradient(to right, #e8b20a, #f5d060, #e8b20a); }
        .body { padding: 36px 40px; color: #333333; font-size: 15px; line-height: 1.7; }
        .btn { display: inline-block; margin: 24px 0; padding: 14px 32px; background-color: #1a3a5c; color: #ffffff !important; text-decoration: none; border-radius: 5px; font-size: 15px; font-weight: bold; letter-spacing: 0.5px; }
        .footer { background-color: #f0f0f0; padding: 20px 30px; text-align: center; font-size: 12px; color: #888888; border-top: 1px solid #e0e0e0; }
        .footer a { color: #1a3a5c; text-decoration: none; }
    </style>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrapper">
    <div class="container">
        <div class="header">
            <h1>Liga Deportiva de Lavalle</h1>
            <p>Mendoza, Argentina</p>
        </div>
        <div class="accent-bar"></div>
        <div class="body">
            <?= $content ?>
        </div>
        <div class="footer">
            Este es un mensaje automático, por favor no respondas este correo.<br>
            &copy; <?= date('Y') ?> Liga Deportiva de Lavalle &mdash; Todos los derechos reservados.
        </div>
    </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage();
