<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "club".
 *
 * @property int $id
 * @property string $nombre
 * @property string $razon_social
 * @property string $cuit_cuil
 * @property string $zona
 * @property string|null $direccion
 * @property string|null $telefono
 * @property string|null $email
 * @property string|null $presidente
 * @property string|null $estadio
 * @property int|null $anio_fundacion
 * @property string|null $logo
 * @property string|null $instagram
 * @property string|null $facebook
 * @property string|null $color_primario
 * @property string|null $color_secundario
 * @property int $activo
 * @property int $created_at
 * @property int $updated_at
 */
class Club extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public static function tableName()
    {
        return 'club';
    }

    public function rules()
    {
        return [
            [['direccion', 'telefono', 'email', 'presidente', 'estadio', 'anio_fundacion', 'logo', 'instagram', 'facebook', 'color_primario', 'color_secundario'], 'default', 'value' => null],
            [['activo'], 'default', 'value' => 1],
            [['nombre', 'razon_social', 'cuit_cuil', 'zona'], 'required'],
            [['anio_fundacion', 'activo', 'created_at', 'updated_at'], 'integer'],
            [['nombre', 'zona', 'email', 'presidente', 'estadio', 'instagram', 'facebook'], 'string', 'max' => 100],
            [['razon_social'], 'string', 'max' => 150],
            [['cuit_cuil'], 'string', 'max' => 13],
            [['direccion'], 'string', 'max' => 200],
            [['telefono'], 'string', 'max' => 20],
            [['logo'], 'string', 'max' => 255],
            [['color_primario', 'color_secundario'], 'string', 'max' => 7],
            [['cuit_cuil'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'razon_social' => 'Razón Social',
            'cuit_cuil' => 'CUIT/CUIL',
            'zona' => 'Zona',
            'direccion' => 'Dirección',
            'telefono' => 'Teléfono',
            'email' => 'Email',
            'presidente' => 'Presidente',
            'estadio' => 'Estadio',
            'anio_fundacion' => 'Año de Fundación',
            'logo' => 'Logo',
            'instagram' => 'Instagram',
            'facebook' => 'Facebook',
            'color_primario' => 'Color Primario',
            'color_secundario' => 'Color Secundario',
            'activo' => 'Activo',
            'created_at' => 'Creado',
            'updated_at' => 'Actualizado',
        ];
    }

    // Relaciones
    public function getJugadores()
    {
        return $this->hasMany(Jugador::class, ['club_id' => 'id']);
    }

    public function getUsers()
    {
        return $this->hasMany(User::class, ['club_id' => 'id']);
    }

    public function getFechasLocal()
    {
        return $this->hasMany(Fechas::class, ['club_local_id' => 'id']);
    }

    public function getFechasVisitante()
    {
        return $this->hasMany(Fechas::class, ['club_visitante_id' => 'id']);
    }

    public function getPartidosLocal()
    {
        return $this->hasMany(Partidos::class, ['club_local_id' => 'id']);
    }

    public function getPartidosVisitante()
    {
        return $this->hasMany(Partidos::class, ['club_visitante_id' => 'id']);
    }
}
