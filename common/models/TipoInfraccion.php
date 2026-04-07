<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tipo_infraccion".
 *
 * @property int $id
 * @property string $nombre
 * @property string|null $descripcion
 * @property string|null $sancion_descripcion
 * @property int|null $sancion_fechas_min
 * @property int|null $sancion_fechas_max
 */
class TipoInfraccion extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_infraccion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcion', 'sancion_descripcion', 'sancion_fechas_min', 'sancion_fechas_max'], 'default', 'value' => null],
            [['nombre'], 'required'],
            [['descripcion'], 'string'],
            [['sancion_fechas_min', 'sancion_fechas_max'], 'integer'],
            [['nombre', 'sancion_descripcion'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'descripcion' => 'Descripción',
            'sancion_descripcion' => 'Sanción Orientativa',
            'sancion_fechas_min' => 'Fechas Mín.',
            'sancion_fechas_max' => 'Fechas Máx.',
        ];
    }

    // Relaciones
    public function getInformeDetalles()
    {
        return $this->hasMany(InformeDetalle::class, ['tipo_infraccion_id' => 'id']);
    }
}
