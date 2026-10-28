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

    /**
     * Carga la lista de ambos clubes de un partido. Omite los que ya estén cargados.
     *
     * Por cada club, el origen de los jugadores es (en orden de prioridad):
     *  1. La última lista de ese club en un partido de la misma categoría — jugador
     *     y remera incluidos, aunque sean de otra categoría (ej. un reserva en primera).
     *  2. El plantel maestro (Buena Fe), si la categoría tiene permite_bf=1.
     *  3. Todos los jugadores del club en la categoría del partido.
     * En todos los casos se re-valida la elegibilidad vigente (pases) y se omiten suspendidos.
     *
     * @return array [insertados, omitidos por suspensión]
     */
    public static function poblarPartido(Partidos $partido): array
    {
        $categoria = Categoria::find()->where(['nombre' => $partido->categoria])->one();
        $catId     = $categoria ? $categoria->id : null;

        $insertados = 0;
        $omitidos   = 0;

        foreach ([$partido->club_local_id, $partido->club_visitante_id] as $clubId) {
            $candidatos = self::candidatosUltimaLista($partido, $clubId);

            if (empty($candidatos) && $categoria && $categoria->permite_bf) {
                $plantel = self::find()
                    ->with('jugador')
                    ->where(['tipo_lista' => self::TIPO_BUENA_FE, 'club_id' => $clubId])
                    ->all();

                foreach ($plantel as $entryPlantel) {
                    $jugador = $entryPlantel->jugador;
                    if (!$jugador || !self::esElegible($jugador, $clubId)) {
                        continue;
                    }
                    if ($catId && $jugador->categoria_id != $catId) {
                        continue;
                    }
                    $candidatos[] = ['jugador' => $jugador, 'remera' => $entryPlantel->remera];
                }
            }

            // Fallback: sin lista previa ni plantel para esta categoría
            if (empty($candidatos)) {
                $q = Jugador::find()->where([
                    'or',
                    ['and', ['club_id' => $clubId], ['club_pase_id' => null]],
                    ['club_pase_id' => $clubId],
                ]);
                if ($catId) {
                    $q->andWhere(['categoria_id' => $catId]);
                }
                foreach ($q->orderBy('nombre')->all() as $jugador) {
                    $candidatos[] = ['jugador' => $jugador, 'remera' => null];
                }
            }

            foreach ($candidatos as $c) {
                $jugador = $c['jugador'];

                if ($jugador->suspendido) {
                    $omitidos++;
                    continue;
                }

                $existe = self::find()->where([
                    'partido_id' => $partido->id,
                    'club_id'    => $clubId,
                    'jugador_id' => $jugador->id,
                ])->exists();

                if (!$existe) {
                    $entry              = new self();
                    $entry->tipo_lista  = self::TIPO_PARTIDO;
                    $entry->partido_id  = $partido->id;
                    $entry->club_id     = $clubId;
                    $entry->jugador_id  = $jugador->id;
                    $entry->remera      = $c['remera'];
                    $entry->save(false);
                    $insertados++;
                }
            }
        }

        return [$insertados, $omitidos];
    }

    /** Jugadores (con remera) de la última lista del club en otro partido de la misma categoría. */
    private static function candidatosUltimaLista(Partidos $partido, $clubId): array
    {
        $ultimoPartidoId = self::find()
            ->select('lista_jugadores.partido_id')
            ->innerJoin('partidos', 'partidos.id = lista_jugadores.partido_id')
            ->where([
                'lista_jugadores.tipo_lista' => self::TIPO_PARTIDO,
                'lista_jugadores.club_id'    => $clubId,
                'partidos.categoria'         => $partido->categoria,
            ])
            ->andWhere(['<>', 'lista_jugadores.partido_id', $partido->id])
            ->orderBy(['lista_jugadores.partido_id' => SORT_DESC])
            ->limit(1)
            ->scalar();

        if (!$ultimoPartidoId) {
            return [];
        }

        $entries = self::find()
            ->with('jugador')
            ->where(['partido_id' => $ultimoPartidoId, 'club_id' => $clubId])
            ->orderBy(['remera' => SORT_ASC])
            ->all();

        $candidatos = [];
        foreach ($entries as $e) {
            // Sin filtro de categoría: la lista previa puede mezclar categorías a propósito
            if ($e->jugador && self::esElegible($e->jugador, $clubId)) {
                $candidatos[] = ['jugador' => $e->jugador, 'remera' => $e->remera];
            }
        }
        return $candidatos;
    }

    /** El jugador juega para el club: propio sin pase a otro, o con pase hacia este. */
    private static function esElegible(Jugador $jugador, $clubId): bool
    {
        return ($jugador->club_id == $clubId && $jugador->club_pase_id === null)
            || $jugador->club_pase_id == $clubId;
    }
}
