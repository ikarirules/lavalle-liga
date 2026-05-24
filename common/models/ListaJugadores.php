<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * @property int         $id
 * @property string      $tipo_lista   'buena_fe' | 'partido'
 * @property int|null    $partido_id
 * @property int         $club_id
 * @property int         $jugador_id
 * @property int|null    $remera       0 = DT, 1-99 = número, NULL = sin asignar
 * @property int         $created_by
 * @property int|null    $updated_by
 * @property int         $created_at
 * @property int         $updated_at
 *
 * @property Partidos    $partido
 * @property Club        $club
 * @property Jugador     $jugador
 */
class ListaJugadores extends \yii\db\ActiveRecord
{
    const TIPO_BUENA_FE = 'buena_fe';
    const TIPO_PARTIDO  = 'partido';


    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    public static function tableName()
    {
        return 'lista_jugadores';
    }

    public function rules()
    {
        return [
            [['partido_id', 'remera', 'updated_by'], 'default', 'value' => null],
            [['tipo_lista'], 'default', 'value' => self::TIPO_PARTIDO],
            [['club_id', 'jugador_id'], 'required'],
            [['partido_id', 'club_id', 'jugador_id', 'remera',
              'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['remera'], 'integer', 'min' => 1, 'max' => 22],
            ['tipo_lista', 'in', 'range' => array_keys(self::optsTipoLista())],
            ['partido_id', 'required', 'when' => fn($m) => $m->tipo_lista === self::TIPO_PARTIDO],
            ['partido_id', 'exist', 'skipOnEmpty' => true,
             'targetClass' => Partidos::class, 'targetAttribute' => ['partido_id' => 'id']],
            ['club_id',    'exist', 'targetClass' => Club::class,    'targetAttribute' => ['club_id'    => 'id']],
            ['jugador_id', 'exist', 'targetClass' => Jugador::class, 'targetAttribute' => ['jugador_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'tipo_lista' => 'Tipo de Lista',
            'partido_id' => 'Partido',
            'club_id'    => 'Club',
            'jugador_id' => 'Jugador',
            'remera'     => 'Remera',
            'created_by' => 'Creado por',
            'updated_by' => 'Actualizado por',
            'created_at' => 'Creado',
            'updated_at' => 'Actualizado',
        ];
    }

    public static function optsTipoLista(): array
    {
        return [
            self::TIPO_BUENA_FE => 'Buena Fe',
            self::TIPO_PARTIDO  => 'Partido',
        ];
    }

    /** Opciones para el dropdown de remera: 1-99. */
    public static function optsRemera(): array
    {
        $opts = ['' => '—'];
        for ($i = 1; $i <= 22; $i++) {
            $opts[$i] = (string) $i;
        }
        return $opts;
    }

    /** Texto legible de la remera (null → "—"). */
    public function displayRemera(): string
    {
        if ($this->remera === null) return '—';
        return (string) $this->remera;
    }

    public function getPartido()
    {
        return $this->hasOne(Partidos::class, ['id' => 'partido_id']);
    }

    public function getClub()
    {
        return $this->hasOne(Club::class, ['id' => 'club_id']);
    }

    public function getJugador()
    {
        return $this->hasOne(Jugador::class, ['id' => 'jugador_id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }
}
