<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "fechas".
 */
class Fechas extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    public static function tableName()
    {
        return 'fechas';
    }

    public function rules()
    {
        return [
            [['torneo_id', 'fecha_reprogramada_1', 'fecha_reprogramada_2', 'fecha_jugada', 'arbitro_id', 'observaciones', 'updated_by'], 'default', 'value' => null],
            [['numero_fecha', 'fecha_programada', 'club_local_id', 'club_visitante_id'], 'required'],
            [['numero_fecha', 'torneo_id', 'club_local_id', 'club_visitante_id', 'arbitro_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['fecha_programada', 'fecha_reprogramada_1', 'fecha_reprogramada_2', 'fecha_jugada'], 'safe'],
            [['observaciones'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'numero_fecha' => 'N° Fecha',
            'torneo_id' => 'Torneo',
            'fecha_programada' => 'Fecha Programada',
            'fecha_reprogramada_1' => '1° Reprogramación',
            'fecha_reprogramada_2' => '2° Reprogramación',
            'fecha_jugada' => 'Fecha Jugada',
            'club_local_id' => 'Club Local',
            'club_visitante_id' => 'Club Visitante',
            'arbitro_id' => 'Árbitro',
            'observaciones' => 'Observaciones',
            'created_by' => 'Creado por',
            'updated_by' => 'Actualizado por',
            'created_at' => 'Creado',
            'updated_at' => 'Actualizado',
        ];
    }

    // Relaciones
    public function getTorneo()
    {
        return $this->hasOne(Torneo::class, ['id' => 'torneo_id']);
    }

    public function getClubLocal()
    {
        return $this->hasOne(Club::class, ['id' => 'club_local_id']);
    }

    public function getClubVisitante()
    {
        return $this->hasOne(Club::class, ['id' => 'club_visitante_id']);
    }

    public function getArbitro()
    {
        return $this->hasOne(User::class, ['id' => 'arbitro_id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public function getPartidos()
    {
        return $this->hasMany(Partidos::class, ['fecha_id' => 'id']);
    }
}
