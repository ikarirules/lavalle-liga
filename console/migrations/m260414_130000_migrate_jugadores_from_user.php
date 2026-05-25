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
        echo "  > Migración de datos históricos, se omite en instalaciones nuevas.\n";
        return true;
    }

    public function safeDown()
    {
        return true;
    }
}
