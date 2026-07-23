<?php

use yii\db\Migration;

/**
 * Antes de este cambio, InformeArbitralController escribía la suspensión directamente en
 * jugador.numero_fecha_suspension / cant_fechas_suspension. Ahora Jugador::getSuspendido()
 * también calcula la suspensión dinámicamente a partir de informe_detalle (con soporte para
 * levantar/revertir por infracción), así que esos valores legacy quedan duplicando información
 * y ya no se pueden "levantar" desde el nuevo botón.
 *
 * Esta migración limpia esos campos SOLO cuando el valor legacy coincide con una infracción
 * existente en informe_detalle (es decir, fue generado por el flujo automático). Si un jugador
 * tiene esos campos cargados pero no hay ninguna infracción que lo explique, se deja intacto
 * por tratarse posiblemente de una suspensión cargada a mano.
 */
class m260723_110000_backfill_clear_legacy_suspension_fields extends Migration
{
    public function up()
    {
        $jugadores = \common\models\Jugador::find()
            ->where(['not', ['numero_fecha_suspension' => null]])
            ->orWhere(['>', 'cant_fechas_suspension', 0])
            ->all();

        $cleared = 0;
        $kept    = 0;

        foreach ($jugadores as $jugador) {
            $origenEncontrado = false;

            foreach ($jugador->informeDetalles as $detalle) {
                $tipo = $detalle->tipoInfraccion;
                if (!$tipo || $tipo->sancion_fechas_min <= 0) {
                    continue;
                }

                $fecha = ($detalle->informe && $detalle->informe->partido) ? $detalle->informe->partido->fecha : null;
                if (!$fecha) {
                    continue;
                }

                if ((int)$fecha->numero_fecha === (int)$jugador->numero_fecha_suspension
                    && (int)$tipo->sancion_fechas_min === (int)$jugador->cant_fechas_suspension) {
                    $origenEncontrado = true;
                    break;
                }
            }

            if ($origenEncontrado) {
                $jugador->numero_fecha_suspension = null;
                $jugador->cant_fechas_suspension  = 0;
                $jugador->save(false);
                $cleared++;
                echo "  limpiado: jugador #{$jugador->id} ({$jugador->nombre})\n";
            } else {
                $kept++;
                echo "  intacto (posible suspensión manual): jugador #{$jugador->id} ({$jugador->nombre})\n";
            }
        }

        echo "Total: {$cleared} limpiados, {$kept} dejados intactos.\n";
    }

    public function down()
    {
        echo "m260723_110000_backfill_clear_legacy_suspension_fields no es reversible: no guarda los valores previos.\n";
        return false;
    }
}
