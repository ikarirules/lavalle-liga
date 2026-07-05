<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;

/**
 * @property int $id
 * @property int $jugador_id
 * @property int $informe_detalle_id
 * @property float $monto
 * @property int $pagado
 * @property string|null $fecha_pago
 * @property string|null $observaciones
 */
class Multa extends \yii\db\ActiveRecord
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
        return 'multa';
    }

    public function rules()
    {
        return [
            [['fecha_pago', 'observaciones'], 'default', 'value' => null],
            [['jugador_id', 'informe_detalle_id', 'monto'], 'required'],
            [['jugador_id', 'informe_detalle_id', 'pagado'], 'integer'],
            [['monto'], 'number'],
            [['fecha_pago'], 'date', 'format' => 'php:Y-m-d'],
            [['observaciones'], 'string', 'max' => 255],
            [['jugador_id'], 'exist', 'targetClass' => Jugador::class, 'targetAttribute' => 'id'],
            [['informe_detalle_id'], 'exist', 'targetClass' => InformeDetalle::class, 'targetAttribute' => 'id'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'                 => 'ID',
            'jugador_id'         => 'Jugador',
            'informe_detalle_id' => 'Infracción',
            'monto'              => 'Monto ($)',
            'pagado'             => 'Pagado',
            'fecha_pago'         => 'Fecha de Pago',
            'observaciones'      => 'Observaciones',
            'created_at'         => 'Creado',
            'updated_at'         => 'Actualizado',
        ];
    }

    public function getJugador()
    {
        return $this->hasOne(Jugador::class, ['id' => 'jugador_id']);
    }

    public function getInformeDetalle()
    {
        return $this->hasOne(InformeDetalle::class, ['id' => 'informe_detalle_id']);
    }

    /**
     * Cuenta cuántas multas previas tiene el jugador (reincidencia).
     * No cuenta la instancia actual.
     */
    public function getReincidencia(): int
    {
        return (int) static::find()
            ->where(['jugador_id' => $this->jugador_id])
            ->andWhere(['<>', 'id', $this->id ?? 0])
            ->count();
    }

    /**
     * Cuenta todas las multas (pagadas o no) de un jugador — para usar antes de guardar.
     */
    public static function contarPorJugador(int $jugadorId): int
    {
        return (int) static::find()->where(['jugador_id' => $jugadorId])->count();
    }
}
