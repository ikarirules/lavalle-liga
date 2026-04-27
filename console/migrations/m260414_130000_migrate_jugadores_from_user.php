<?php

use yii\db\Migration;

/**
 * Migra los usuarios con rol 'jugador' a la tabla jugador.
 * Los campos nombre, dni y categoria quedan para completar desde el CRUD.
 */
class m260414_130000_migrate_jugadores_from_user extends Migration
{
    public function safeUp()
    {
        $now = time();

        $this->execute("
            INSERT INTO jugador (nombre, dni, categoria_id, club_id, cant_fechas_suspension, created_at, updated_at)
            SELECT
                u.username                    AS nombre,
                CONCAT('TEMP-', u.id)         AS dni,
                'primera'                     AS categoria_id,
                u.club_id                     AS club_id,
                0                             AS cant_fechas_suspension,
                {$now}                        AS created_at,
                {$now}                        AS updated_at
            FROM user u
            INNER JOIN auth_assignment aa ON aa.user_id = u.id AND aa.item_name = 'jugador'
            WHERE u.id NOT IN (
                SELECT DISTINCT j2.id FROM jugador j2
                WHERE j2.dni = CONCAT('TEMP-', u.id)
            )
        ");

        $count = $this->db->createCommand("SELECT COUNT(*) FROM jugador WHERE dni LIKE 'TEMP-%'")->queryScalar();
        echo "  > {$count} jugadores migrados. Completar nombre, DNI y categoría desde /jugador/index.\n";
    }

    public function safeDown()
    {
        $this->execute("DELETE FROM jugador WHERE dni LIKE 'TEMP-%'");
        echo "  > Jugadores temporales eliminados.\n";
    }
}
