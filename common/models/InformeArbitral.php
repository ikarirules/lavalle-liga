<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "informe_arbitral".
 */
class InformeArbitral extends \yii\db\ActiveRecord
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
        return 'informe_arbitral';
    }

    public function rules()
    {
        return [
            [['asistente_id', 'observaciones', 'updated_by'], 'default', 'value' => null],
            [['partido_id', 'arbitro_id'], 'required'],
            [['partido_id', 'arbitro_id', 'asistente_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['observaciones'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'partido_id' => 'Partido',
            'arbitro_id' => 'Árbitro',
            'asistente_id' => 'Asistente',
            'observaciones' => 'Observaciones',
            'created_by' => 'Creado por',
            'updated_by' => 'Actualizado por',
            'created_at' => 'Creado',
            'updated_at' => 'Actualizado',
        ];
    }

    // Relaciones
    public function getPartido()
    {
        return $this->hasOne(Partidos::class, ['id' => 'partido_id']);
    }

    public function getArbitro()
    {
        return $this->hasOne(User::class, ['id' => 'arbitro_id']);
    }

    public function getAsistente()
    {
        return $this->hasOne(User::class, ['id' => 'asistente_id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public function getDetalles()
    {
        return $this->hasMany(InformeDetalle::class, ['informe_id' => 'id']);
    }

    public function getGoles()
    {
        return $this->hasMany(InformeGol::class, ['informe_id' => 'id']);
    }
}
