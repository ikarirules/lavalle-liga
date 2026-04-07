<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * This is the model class for table "torneo".
 */
class Torneo extends \yii\db\ActiveRecord
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
        return 'torneo';
    }

    public function rules()
    {
        return [
            [['fecha_inicio', 'fecha_fin', 'updated_by'], 'default', 'value' => null],
            [['activo'], 'default', 'value' => 1],
            [['nombre', 'anio'], 'required'],
            [['anio', 'activo', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['fecha_inicio', 'fecha_fin'], 'safe'],
            [['nombre'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'anio' => 'Año',
            'fecha_inicio' => 'Fecha de Inicio',
            'fecha_fin' => 'Fecha de Fin',
            'activo' => 'Activo',
            'created_by' => 'Creado por',
            'updated_by' => 'Actualizado por',
            'created_at' => 'Creado',
            'updated_at' => 'Actualizado',
        ];
    }

    // Relaciones
    public function getFechas()
    {
        return $this->hasMany(Fechas::class, ['torneo_id' => 'id']);
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
