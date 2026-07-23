<?php

namespace common\models;

use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

/**
 * @property int $id
 * @property string $nombre
 * @property string $dni
 * @property string|null $fecha_nacimiento
 * @property string|null $numero_carnet
 * @property string|null $foto_carnet
 * @property int $categoria_id
 * @property int $club_id
 * @property int|null $club_pase_id
 * @property int|null $numero_fecha_suspension
 * @property int $cant_fechas_suspension
 * @property int $created_at
 * @property int $updated_at
 */
class Jugador extends \yii\db\ActiveRecord
{
    /** Archivo subido (no se guarda en BD, solo para el form) */
    public $foto_file;

    public function behaviors()
    {
        return [TimestampBehavior::class];
    }

    public static function tableName()
    {
        return 'jugador';
    }

    public function rules()
    {
        return [
            [['nombre', 'dni', 'categoria_id', 'club_id'], 'required'],
            [['categoria_id', 'club_id', 'club_pase_id', 'numero_fecha_suspension', 'cant_fechas_suspension'], 'integer'],
            [['club_pase_id'], 'default', 'value' => null],
            [['club_pase_id'], 'exist', 'skipOnEmpty' => true, 'targetClass' => Club::class, 'targetAttribute' => 'id'],
            [['cant_fechas_suspension'], 'default', 'value' => 0],
            [['numero_fecha_suspension'], 'default', 'value' => null],
            [['fecha_nacimiento'], 'date', 'format' => 'php:Y-m-d'],
            [['fecha_nacimiento'], 'default', 'value' => null],
            [['nombre'], 'string', 'max' => 255],
            [['dni'], 'string', 'max' => 20],
            [['dni'], 'unique'],
            [['numero_carnet'], 'string', 'max' => 20],
            [['numero_carnet'], 'unique'],
            [['foto_carnet'], 'string', 'max' => 255],
            [['categoria_id'], 'exist', 'targetClass' => Categoria::class, 'targetAttribute' => 'id'],
            [['club_id'], 'exist', 'targetClass' => Club::class, 'targetAttribute' => 'id'],
            [['foto_file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'jpg,jpeg,png,webp', 'maxSize' => 2 * 1024 * 1024],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'                      => 'ID',
            'nombre'                  => 'Nombre',
            'dni'                     => 'DNI',
            'fecha_nacimiento'        => 'Fecha de Nacimiento',
            'numero_carnet'           => 'N° Carnet',
            'foto_carnet'             => 'Foto Carnet',
            'foto_file'               => 'Foto Carnet',
            'categoria_id'            => 'Categoría',
            'club_id'                 => 'Club',
            'club_pase_id'            => 'Club del Pase',
            'numero_fecha_suspension' => 'N° Fecha Suspensión',
            'cant_fechas_suspension'  => 'Fechas de Suspensión',
            'created_at'              => 'Creado',
            'updated_at'              => 'Actualizado',
        ];
    }

    /**
     * Procesa y guarda la foto subida. Devuelve true si todo OK o no había foto.
     */
    public function uploadFoto()
    {
        $this->foto_file = UploadedFile::getInstance($this, 'foto_file');

        if (!$this->foto_file) {
            return true;
        }

        $dir      = \Yii::getAlias('@frontend/web/uploads/carnets/');
        $filename = 'jugador_' . $this->id . '.' . $this->foto_file->extension;
        $path     = $dir . $filename;

        if ($this->foto_file->saveAs($path)) {
            // Borrar foto anterior si existe y tiene distinto nombre
            if ($this->foto_carnet && $this->foto_carnet !== $filename) {
                @unlink($dir . $this->foto_carnet);
            }
            $this->foto_carnet = $filename;
            return true;
        }

        return false;
    }

    /**
     * URL pública de la foto o null si no tiene.
     */
    public function getFotoUrl()
    {
        if (!$this->foto_carnet) {
            return null;
        }
        return \Yii::$app->request->baseUrl . '/uploads/carnets/' . $this->foto_carnet;
    }

    /**
     * Calcula si el jugador está suspendido: por la suspensión manual (numero_fecha_suspension/
     * cant_fechas_suspension, cargada directamente en su ficha) o por alguna sanción vigente
     * generada desde un informe arbitral (ver InformeDetalle::getSancionVigente()).
     */
    public function getSuspendido()
    {
        if ($this->suspendidoManual()) {
            return true;
        }

        foreach ($this->informeDetalles as $detalle) {
            if ($detalle->sancionVigente) {
                return true;
            }
        }

        return false;
    }

    protected function suspendidoManual(): bool
    {
        if (!$this->numero_fecha_suspension || !$this->cant_fechas_suspension) {
            return false;
        }

        $ultimaRonda = $this->numero_fecha_suspension + $this->cant_fechas_suspension;

        $fecha = Fechas::find()
            ->where(['numero_fecha' => $ultimaRonda])
            ->orderBy(['fecha_programada' => SORT_DESC])
            ->one();

        if (!$fecha || !$fecha->fecha_programada) {
            return true;
        }

        $fechaEfectiva = $fecha->fecha_reprogramada_2
            ?? $fecha->fecha_reprogramada_1
            ?? $fecha->fecha_programada;

        return date('Y-m-d') <= date('Y-m-d', strtotime($fechaEfectiva));
    }

    public function getInformeDetalles()
    {
        return $this->hasMany(InformeDetalle::class, ['jugador_id' => 'id']);
    }

    public function getCategoria()
    {
        return $this->hasOne(Categoria::class, ['id' => 'categoria_id']);
    }

    public function getClub()
    {
        return $this->hasOne(Club::class, ['id' => 'club_id']);
    }

    public function getClubPase()
    {
        return $this->hasOne(Club::class, ['id' => 'club_pase_id']);
    }
}
