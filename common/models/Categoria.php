<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;

/**
 * @property int $id
 * @property string $nombre
 * @property int $permite_bf
 * @property int $activo
 * @property string|null $fecha_desde
 * @property string|null $fecha_hasta
 * @property int $created_at
 * @property int $updated_at
 */
class Categoria extends \yii\db\ActiveRecord
{
    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public static function tableName()
    {
        return 'categoria';
    }

    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['nombre'], 'string', 'max' => 50],
            [['nombre'], 'unique'],
            [['permite_bf', 'activo'], 'integer'],
            [['permite_bf'], 'default', 'value' => 0],
            [['activo'], 'default', 'value' => 1],
            [['fecha_desde', 'fecha_hasta'], 'date', 'format' => 'php:Y-m-d'],
            [['fecha_desde', 'fecha_hasta'], 'default', 'value' => null],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'nombre'      => 'Nombre',
            'permite_bf'  => 'Permite Buena Fe',
            'activo'      => 'Activo',
            'fecha_desde' => 'Nacidos desde',
            'fecha_hasta' => 'Nacidos hasta',
            'created_at'  => 'Creado',
            'updated_at'  => 'Actualizado',
        ];
    }

    public function getJugadores()
    {
        return $this->hasMany(Jugador::class, ['categoria_id' => 'id']);
    }

    /**
     * Devuelve array [id => nombre] para dropdowns, solo categorías activas.
     */
    public static function lista()
    {
        return \yii\helpers\ArrayHelper::map(
            self::find()->where(['activo' => 1])->orderBy('nombre')->all(),
            'id',
            'nombre'
        );
    }

    /**
     * Busca la categoría activa cuyo rango de fechas incluye $fechaNacimiento.
     * Devuelve null si ninguna categoría tiene fechas configuradas que coincidan.
     */
    public static function findByFechaNacimiento($fechaNacimiento)
    {
        if (!$fechaNacimiento) {
            return null;
        }
        return self::find()
            ->where(['activo' => 1])
            ->andWhere(['<=', 'fecha_desde', $fechaNacimiento])
            ->andWhere(['>=', 'fecha_hasta', $fechaNacimiento])
            ->andWhere(['not', ['fecha_desde' => null]])
            ->andWhere(['not', ['fecha_hasta' => null]])
            ->one();
    }
}
