<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\User $user */

Yii::$app->urlManager->hostInfo = 'https://ligadeportivalavalle.com';
Yii::$app->urlManager->baseUrl = '';
$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
$this->title = 'Recuperación de contraseña';
?>
<p>Hola <strong><?= Html::encode($user->username) ?></strong>,</p>

<p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en la <strong>Liga Deportiva de Lavalle</strong>.</p>

<p>Hacé clic en el botón a continuación para crear una nueva contraseña:</p>

<p style="text-align: center;">
    <a href="<?= Html::encode($resetLink) ?>" class="btn">Restablecer contraseña</a>
</p>

<p style="font-size: 13px; color: #666;">
    Si el botón no funciona, copiá y pegá el siguiente enlace en tu navegador:<br>
    <a href="<?= Html::encode($resetLink) ?>" style="color: #1a3a5c; word-break: break-all;"><?= Html::encode($resetLink) ?></a>
</p>

<p style="font-size: 13px; color: #666;">
    Este enlace expirará en <strong>24 horas</strong>. Si no solicitaste restablecer tu contraseña, podés ignorar este correo y tu cuenta seguirá siendo la misma.
</p>
