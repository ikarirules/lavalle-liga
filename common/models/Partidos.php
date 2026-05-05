<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "partidos".
 *
 * @property int $id
 * @property int $fecha_id
 * @property string $categoria
 * @property int $club_local_id
 * @property int $club_visitante_id
 * @property string|null $cancha
 * @property string $estado
 * @property string|null $arbitro
 * @property string|null $asistente1
 * @property string|null $asistente2
 * @property string|null $asistente3
 * @property int $created_by
 * @property int|null $updated_by
 * @property int $created_at
 * @property int $updated_at
 */
class Partidos extends \yii\db\ActiveRecord
{

    /**
     * ENUM field values
     */
    const ESTADO_PROGRAMADA = 'programada';
    const ESTADO_SUSPENDIDA = 'suspendida';
    const ESTADO_POSTERGADA = 'postergada';
    const ESTADO_JUGADA = 'jugada';

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
        ];
    }

    public static function tableName()
    {
        return 'partidos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cancha', 'arbitro', 'asistente1', 'asistente2', 'asistente3', 'updated_by'], 'default', 'value' => null],
            [['estado'], 'default', 'value' => 'programada'],
            [['fecha_id', 'categoria', 'club_local_id', 'club_visitante_id'], 'required'],
            [['fecha_id', 'club_local_id', 'club_visitante_id', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['estado'], 'string'],
            [['categoria'], 'string', 'max' => 50],
            [['cancha'], 'string', 'max' => 100],
            [['arbitro', 'asistente1', 'asistente2', 'asistente3'], 'string', 'max' => 100],
            ['estado', 'in', 'range' => array_keys(self::optsEstado())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fecha_id' => 'Fecha ID',
            'categoria' => 'Categoria',
            'club_local_id' => 'Club Local ID',
            'club_visitante_id' => 'Club Visitante ID',
            'cancha' => 'Cancha',
            'estado' => 'Estado',
            'arbitro' => 'Árbitro',
            'asistente1' => 'Asistente 1',
            'asistente2' => 'Asistente 2',
            'asistente3' => 'Asistente 3',
            'created_by' => 'Creado por',
            'updated_by' => 'Actualizado por',
            'created_at' => 'Creado',
            'updated_at' => 'Actualizado',
        ];
    }

    // Relaciones
    public function getFecha()
    {
        return $this->hasOne(Fechas::class, ['id' => 'fecha_id']);
    }

    public function getClubLocal()
    {
        return $this->hasOne(Club::class, ['id' => 'club_local_id']);
    }

    public function getClubVisitante()
    {
        return $this->hasOne(Club::class, ['id' => 'club_visitante_id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    public function getInformeArbitral()
    {
        return $this->hasOne(InformeArbitral::class, ['partido_id' => 'id']);
    }

    /**
     * column estado ENUM value labels
     * @return string[]
     */
    public static function optsEstado()
    {
        return [
            self::ESTADO_PROGRAMADA => 'programada',
            self::ESTADO_SUSPENDIDA => 'suspendida',
            self::ESTADO_POSTERGADA => 'postergada',
            self::ESTADO_JUGADA => 'jugada',
        ];
    }

    /**
     * @return string
     */
    public function displayEstado()
    {
        return self::optsEstado()[$this->estado];
    }

    /**
     * @return bool
     */
    public function isEstadoProgramada()
    {
        return $this->estado === self::ESTADO_PROGRAMADA;
    }

    public function setEstadoToProgramada()
    {
        $this->estado = self::ESTADO_PROGRAMADA;
    }

    /**
     * @return bool
     */
    public function isEstadoSuspendida()
    {
        return $this->estado === self::ESTADO_SUSPENDIDA;
    }

    public function setEstadoToSuspendida()
    {
        $this->estado = self::ESTADO_SUSPENDIDA;
    }

    /**
     * @return bool
     */
    public function isEstadoPostergada()
    {
        return $this->estado === self::ESTADO_POSTERGADA;
    }

    public function setEstadoToPostergada()
    {
        $this->estado = self::ESTADO_POSTERGADA;
    }

    /**
     * @return bool
     */
    public function isEstadoJugada()
    {
        return $this->estado === self::ESTADO_JUGADA;
    }

    public function setEstadoToJugada()
    {
        $this->estado = self::ESTADO_JUGADA;
    }
}
