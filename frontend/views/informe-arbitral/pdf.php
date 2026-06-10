<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Informe Arbitral #<?= $model->id ?></title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 12px; color: #222; padding: 20px; }

    .no-print { text-align: center; margin-bottom: 20px; }
    .no-print button {
        background: #1a6cb5; color: #fff; border: none; padding: 10px 28px;
        font-size: 14px; border-radius: 4px; cursor: pointer;
    }
    .no-print button:hover { background: #155a9a; }

    .header { text-align: center; border-bottom: 2px solid #1a6cb5; padding-bottom: 10px; margin-bottom: 16px; }
    .header h2 { font-size: 18px; color: #1a6cb5; }
    .header p { font-size: 11px; color: #555; margin-top: 4px; }

    .info-grid { display: flex; gap: 16px; margin-bottom: 16px; }
    .info-box { flex: 1; border: 1px solid #ccc; border-radius: 4px; padding: 10px; }
    .info-box h4 { font-size: 11px; text-transform: uppercase; color: #1a6cb5; margin-bottom: 6px; letter-spacing: .5px; }
    .info-box p { margin-bottom: 3px; }
    .info-box strong { font-size: 13px; }

    .partido-box { text-align: center; border: 2px solid #1a6cb5; border-radius: 4px; padding: 12px; margin-bottom: 16px; }
    .partido-box .vs { font-size: 22px; font-weight: bold; color: #1a6cb5; }
    .partido-box .equipos { display: flex; justify-content: center; align-items: center; gap: 16px; font-size: 15px; font-weight: bold; }
    .partido-box .meta { font-size: 11px; color: #666; margin-top: 6px; }

    .section-title {
        background: #1a6cb5; color: #fff;
        padding: 5px 10px; font-size: 12px; font-weight: bold;
        margin-bottom: 0; border-radius: 3px 3px 0 0;
    }
    table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    table th { background: #e8f0f9; font-size: 11px; text-align: left; padding: 5px 8px; border: 1px solid #ccc; }
    table td { padding: 5px 8px; border: 1px solid #ccc; font-size: 11px; }
    table tr:nth-child(even) td { background: #f9f9f9; }

    .obs-box { border: 1px solid #ccc; border-radius: 4px; padding: 10px; min-height: 60px; margin-bottom: 16px; font-size: 11px; white-space: pre-wrap; }

    .firma-row { display: flex; gap: 30px; margin-top: 40px; }
    .firma-box { flex: 1; text-align: center; border-top: 1px solid #555; padding-top: 6px; font-size: 11px; }

    @media print {
        .no-print { display: none !important; }
        body { padding: 10px; }
        @page { margin: 15mm; }
    }
</style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()">&#x1F4BE; Descargar / Imprimir PDF</button>
</div>

<div class="header">
    <h2>Informe Arbitral N° <?= $model->id ?></h2>
    <p>Generado el <?= date('d/m/Y H:i') ?></p>
</div>

<?php
$partido    = $model->partido;
$fecha      = $partido ? $partido->fecha : null;
$local      = $partido && $partido->clubLocal      ? $partido->clubLocal->nombre      : '—';
$visitante  = $partido && $partido->clubVisitante  ? $partido->clubVisitante->nombre  : '—';
$categoria  = $partido ? $partido->categoria : '—';
$fechaNum   = $fecha   ? 'Fecha ' . $fecha->numero_fecha : '—';
$fechaDate  = $fecha   ? $fecha->fecha_programada : '—';
?>

<div class="partido-box">
    <div class="equipos">
        <span><?= htmlspecialchars($local) ?></span>
        <span class="vs">VS</span>
        <span><?= htmlspecialchars($visitante) ?></span>
    </div>
    <div class="meta"><?= htmlspecialchars($fechaNum) ?> &nbsp;|&nbsp; <?= htmlspecialchars($fechaDate) ?> &nbsp;|&nbsp; Categoría: <?= htmlspecialchars($categoria) ?></div>
</div>

<div class="info-grid">
    <div class="info-box">
        <h4>Árbitro</h4>
        <strong><?= $model->arbitro ? htmlspecialchars($model->arbitro->username) : '—' ?></strong>
    </div>
    <div class="info-box">
        <h4>Asistente</h4>
        <strong><?= $model->asistente ? htmlspecialchars($model->asistente->username) : '—' ?></strong>
    </div>
</div>

<!-- GOLES -->
<div class="section-title">Goles</div>
<?php
$goles = $model->goles;
if (empty($goles)): ?>
    <table><tr><td style="text-align:center;color:#888;">Sin goles registrados</td></tr></table>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>Club</th>
            <th>Jugador</th>
            <th style="width:80px;text-align:center;">Cantidad</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($goles as $gol): ?>
        <tr>
            <td><?= $gol->club  ? htmlspecialchars($gol->club->nombre)  : $gol->club_id ?></td>
            <td><?= $gol->jugador ? htmlspecialchars($gol->jugador->nombre) : '—' ?></td>
            <td style="text-align:center;"><?= $gol->cantidad ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<!-- INFRACCIONES -->
<div class="section-title">Infracciones</div>
<?php
$detalles = $model->detalles;
if (empty($detalles)): ?>
    <table><tr><td style="text-align:center;color:#888;">Sin infracciones registradas</td></tr></table>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>Club</th>
            <th>Jugador</th>
            <th>Tipo de infracción</th>
            <th style="width:70px;text-align:center;">Minuto</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($detalles as $det): ?>
        <tr>
            <td><?= $det->club ? htmlspecialchars($det->club->nombre) : $det->club_id ?></td>
            <td><?= $det->jugador ? htmlspecialchars($det->jugador->nombre) : '—' ?></td>
            <td><?= $det->tipoInfraccion ? htmlspecialchars($det->tipoInfraccion->nombre) : '—' ?></td>
            <td style="text-align:center;"><?= $det->minuto ?? '—' ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<!-- OBSERVACIONES -->
<div class="section-title" style="margin-top:8px;">Observaciones</div>
<div class="obs-box" style="border-radius:0 0 3px 3px;border-top:none;">
    <?= $model->observaciones ? htmlspecialchars($model->observaciones) : '<em style="color:#aaa;">Sin observaciones</em>' ?>
</div>

<!-- FIRMAS -->
<div class="firma-row">
    <div class="firma-box">Árbitro<br><?= $model->arbitro ? htmlspecialchars($model->arbitro->username) : '' ?></div>
    <div class="firma-box">Asistente<br><?= $model->asistente ? htmlspecialchars($model->asistente->username) : '' ?></div>
</div>

</body>
</html>
