<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "informe_detalle".
 */
class InformeDetalle extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public static function tableName()
    {
        return 'informe_detalle';
    }

    public function rules()
    {
        return [
            [['minuto', 'jugador_id', 'numero_camiseta'], 'default', 'value' => null],
            [['sancion_levantada'], 'default', 'value' => 0],
            [['informe_id', 'club_id', 'tipo_infraccion_id'], 'required'],
            [['informe_id', 'minuto', 'jugador_id', 'numero_camiseta', 'club_id', 'tipo_infraccion_id', 'sancion_levantada', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'informe_id' => 'Informe',
            'minuto' => 'Minuto',
            'jugador_id' => 'Jugador',
            'numero_camiseta' => 'N° Camiseta',
            'club_id' => 'Club',
            'tipo_infraccion_id' => 'Tipo de Infracción',
            'sancion_levantada' => 'Inhabilitación Levantada',
            'created_at' => 'Creado',
            'updated_at' => 'Actualizado',
        ];
    }

    // Relaciones
    public function getInforme()
    {
        return $this->hasOne(InformeArbitral::class, ['id' => 'informe_id']);
    }

    public function getJugador()
    {
        return $this->hasOne(Jugador::class, ['id' => 'jugador_id']);
    }

    public function getClub()
    {
        return $this->hasOne(Club::class, ['id' => 'club_id']);
    }

    public function getTipoInfraccion()
    {
        return $this->hasOne(TipoInfraccion::class, ['id' => 'tipo_infraccion_id']);
    }

    public function getMulta()
    {
        return $this->hasOne(Multa::class, ['informe_detalle_id' => 'id']);
    }

    /**
     * True si esta infracción conlleva una sanción de fechas (más allá de si ya se aplicó o levantó).
     */
    public function tieneSancion(): bool
    {
        return $this->jugador_id !== null
            && $this->tipoInfraccion
            && $this->tipoInfraccion->sancion_fechas_min > 0;
    }

    /**
     * True si la sanción de esta infracción está vigente hoy: no fue levantada manualmente
     * y la ventana de fechas de suspensión (a partir de la fecha del partido) todavía no venció.
     */
    public function getSancionVigente(): bool
    {
        if ($this->sancion_levantada || !$this->tieneSancion()) {
            return false;
        }

        $fecha = $this->informe && $this->informe->partido ? $this->informe->partido->fecha : null;
        if (!$fecha) {
            return false;
        }

        $ultimaRonda = $fecha->numero_fecha + $this->tipoInfraccion->sancion_fechas_min;

        $fechaLimite = Fechas::find()
            ->where(['numero_fecha' => $ultimaRonda])
            ->orderBy(['fecha_programada' => SORT_DESC])
            ->one();

        if (!$fechaLimite || !$fechaLimite->fecha_programada) {
            return true;
        }

        $fechaEfectiva = $fechaLimite->fecha_reprogramada_2
            ?? $fechaLimite->fecha_reprogramada_1
            ?? $fechaLimite->fecha_programada;

        return date('Y-m-d') <= date('Y-m-d', strtotime($fechaEfectiva));
    }
}
