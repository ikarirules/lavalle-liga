<?php

use yii\db\Migration;

/**
 * Replaces remera_local + remera_visitante with a single remera column.
 * 0 = DT/coach, 1-99 = jersey number, NULL = not yet assigned.
 */
class m260503_150000_alter_lista_jugadores_remera extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('{{%lista_jugadores}}', 'remera_local');
        $this->dropColumn('{{%lista_jugadores}}', 'remera_visitante');
        $this->addColumn('{{%lista_jugadores}}', 'remera',
            $this->tinyInteger()->unsigned()->null()->after('jugador_id'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%lista_jugadores}}', 'remera');
        $this->addColumn('{{%lista_jugadores}}', 'remera_local',
            $this->tinyInteger()->null()->after('jugador_id'));
        $this->addColumn('{{%lista_jugadores}}', 'remera_visitante',
            $this->tinyInteger()->null()->after('remera_local'));
    }
}
