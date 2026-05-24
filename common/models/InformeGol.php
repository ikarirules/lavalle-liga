<?php

namespace common\models;

class InformeGol extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'informe_gol';
    }

    public function rules()
    {
        return [
            [['informe_id', 'jugador_id', 'club_id'], 'required'],
            [['informe_id', 'jugador_id', 'club_id', 'cantidad'], 'integer'],
            [['cantidad'], 'default', 'value' => 1],
            [['cantidad'], 'integer', 'min' => 1, 'max' => 20],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'informe_id' => 'Informe',
            'jugador_id' => 'Jugador',
            'club_id'    => 'Club',
            'cantidad'   => 'Cantidad',
        ];
    }

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
}
