<?php

/** @var yii\web\View $this */
/** @var common\models\User $user */

Yii::$app->urlManager->hostInfo = 'https://ligadeportivalavalle.com';
Yii::$app->urlManager->baseUrl = '';
$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['site/reset-password', 'token' => $user->password_reset_token]);
?>
Hola <?= $user->username ?>,

Recibimos una solicitud para restablecer la contraseña de tu cuenta en la Liga Deportiva de Lavalle.

Para crear una nueva contraseña, ingresá al siguiente enlace:

<?= $resetLink ?>

Este enlace expirará en 24 horas.

Si no solicitaste restablecer tu contraseña, podés ignorar este correo.

---
Liga Deportiva de Lavalle - Mendoza, Argentina
Este es un mensaje automático, por favor no respondas este correo.
