<?php

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
require __DIR__ . '/common/config/bootstrap.php';
require __DIR__ . '/console/config/bootstrap.php';

$config = yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/common/config/main.php',
    require __DIR__ . '/common/config/main-local.php',
    require __DIR__ . '/console/config/main.php',
    require __DIR__ . '/console/config/main-local.php'
);

$app = new yii\console\Application($config);

Yii::$app->urlManager->hostInfo = 'http://localhost';
Yii::$app->urlManager->scriptUrl = '/lavalle-liga/frontend/web/index.php';

$destinatario = $argv[1] ?? 'test@example.com';

// Usuario de prueba simulado
$user = new \common\models\User();
$user->username = 'david';
$user->password_reset_token = \Yii::$app->security->generateRandomString() . '_' . time();

$resultado = Yii::$app->mailer->compose(
        ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
        ['user' => $user]
    )
    ->setFrom(['diaz.david1607@gmail.com' => 'Liga Deportiva de Lavalle'])
    ->setTo($destinatario)
    ->setSubject('Recuperación de contraseña - Liga Deportiva de Lavalle')
    ->send();

if ($resultado) {
    echo "Email enviado correctamente a: $destinatario\n";
} else {
    echo "Error al enviar el email.\n\nDetalle del error:\n";
    foreach (Yii::getLogger()->messages as $msg) {
        if ($msg[1] === \yii\log\Logger::LEVEL_ERROR) {
            echo "  " . $msg[0] . "\n";
        }
    }
}
