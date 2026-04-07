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
            [['informe_id', 'club_id', 'tipo_infraccion_id'], 'required'],
            [['informe_id', 'minuto', 'jugador_id', 'numero_camiseta', 'club_id', 'tipo_infraccion_id', 'created_at', 'updated_at'], 'integer'],
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
        return $this->hasOne(User::class, ['id' => 'jugador_id']);
    }

    public function getClub()
    {
        return $this->hasOne(Club::class, ['id' => 'club_id']);
    }

    public function getTipoInfraccion()
    {
        return $this->hasOne(TipoInfraccion::class, ['id' => 'tipo_infraccion_id']);
    }
}
