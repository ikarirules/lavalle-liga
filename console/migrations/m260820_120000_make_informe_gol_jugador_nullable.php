<?php

use yii\db\Migration;

/**
 * Permite cargar un gol sin identificar al jugador (útil cuando el plantel
 * todavía no está completo en el sistema pero hay que cargar el resultado).
 */
class m260820_120000_make_informe_gol_jugador_nullable extends Migration
{
    public function safeUp()
    {
        $this->dropForeignKey('fk_informe_gol_jugador', '{{%informe_gol}}');
        $this->alterColumn('{{%informe_gol}}', 'jugador_id', $this->integer()->null());
        $this->addForeignKey('fk_informe_gol_jugador', '{{%informe_gol}}', 'jugador_id', 'jugador', 'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_informe_gol_jugador', '{{%informe_gol}}');
        $this->alterColumn('{{%informe_gol}}', 'jugador_id', $this->integer()->notNull());
        $this->addForeignKey('fk_informe_gol_jugador', '{{%informe_gol}}', 'jugador_id', 'jugador', 'id', 'CASCADE', 'CASCADE');
    }
}
