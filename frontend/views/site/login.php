<?php

/** @var yii\web\View $this */
/** @var yii\bootstrap5\ActiveForm $form */
/** @var \common\models\LoginForm $model */

use yii\bootstrap5\Html;
use yii\bootstrap5\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Iniciar sesión';
?>

<style>
.login-wrap {
    min-height: calc(100vh - 120px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem 1rem;
}
.login-card {
    background: #0d0d0d;
    border: 1px solid #39ff1440;
    border-radius: 10px;
    padding: 2.5rem 2rem;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 0 30px #39ff1420;
}
.login-card h2 {
    color: #39ff14;
    text-shadow: 0 0 12px #39ff14;
    font-weight: 700;
    margin-bottom: .25rem;
}
.login-card .subtitle {
    color: #555;
    font-size: .9rem;
    margin-bottom: 1.75rem;
}
/* labels */
.login-card .form-label { color: #39ff14; font-size: .85rem; letter-spacing: .04em; text-shadow: 0 0 6px #39ff1460; }

/* inputs */
.login-card .form-control {
    background: #111;
    border: 1px solid #39ff1450;
    color: #e0e0e0;
    border-radius: 6px;
    transition: border-color .2s, box-shadow .2s;
}
.login-card .form-control:focus {
    background: #111;
    border-color: #39ff14;
    box-shadow: 0 0 0 3px #39ff1430;
    color: #fff;
}
.login-card .form-control::placeholder { color: #444; }

/* checkbox */
.login-card .form-check-input {
    background-color: #111;
    border-color: #39ff1450;
}
.login-card .form-check-input:checked {
    background-color: #39ff14;
    border-color: #39ff14;
}
.login-card .form-check-label { color: #aaa; font-size: .87rem; }

/* captcha image */
.login-card img { border: 1px solid #39ff1440; border-radius: 6px; cursor: pointer; }

/* links */
.login-card a { color: #39ff14; text-decoration: none; }
.login-card a:hover { color: #00f5ff; text-shadow: 0 0 8px #00f5ff; }
.login-links { font-size: .82rem; color: #555; margin-top: .5rem; }

/* error messages */
.login-card .invalid-feedback { color: #ff4d6d; }
.login-card .form-control.is-invalid { border-color: #ff4d6d; }

/* submit */
.btn-neon {
    background: transparent;
    border: 2px solid #39ff14;
    color: #39ff14;
    text-shadow: 0 0 8px #39ff14;
    box-shadow: 0 0 10px #39ff1440;
    font-weight: 600;
    letter-spacing: .06em;
    transition: background .2s, color .2s, box-shadow .2s;
    width: 100%;
    padding: .55rem;
    border-radius: 6px;
    margin-top: .5rem;
}
.btn-neon:hover {
    background: #39ff14;
    color: #0a0a0a;
    box-shadow: 0 0 20px #39ff14;
    text-shadow: none;
}
</style>

<div class="login-wrap">
    <div class="login-card">
        <h2>⚽ Liga</h2>
        <p class="subtitle">Iniciá sesión para continuar</p>

        <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true, 'placeholder' => 'Usuario']) ?>

            <?= $form->field($model, 'password')->passwordInput(['placeholder' => '••••••••']) ?>

            <?= $form->field($model, 'rememberMe')->checkbox() ?>

            <?php /* CAPTCHA DESHABILITADO TEMPORALMENTE
            echo $form->field($model, 'verifyCode')->widget(Captcha::class, [
                'captchaAction' => '/site/captcha',
                'template'      => '<div class="mb-2">{image}</div>{input}',
            ])->label('Código de verificación');
            */ ?>

            <div class="login-links mb-3">
                ¿Olvidaste tu contraseña? <?= Html::a('Recuperar', ['site/request-password-reset']) ?>
            </div>

            <?= Html::submitButton('Ingresar', ['class' => 'btn-neon', 'name' => 'login-button']) ?>

        <?php ActiveForm::end(); ?>
    </div>
</div>
