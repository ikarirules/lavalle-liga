<?php

use yii\db\Migration;

/**
 * Alters table `{{%partidos}}`: removes goles_local/goles_visitante, adds arbitro/asistente1/2/3.
 */
class m260503_120000_alter_partidos_table extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('{{%partidos}}', 'goles_local');
        $this->dropColumn('{{%partidos}}', 'goles_visitante');
        $this->addColumn('{{%partidos}}', 'arbitro', $this->string(100)->null()->after('estado'));
        $this->addColumn('{{%partidos}}', 'asistente1', $this->string(100)->null()->after('arbitro'));
        $this->addColumn('{{%partidos}}', 'asistente2', $this->string(100)->null()->after('asistente1'));
        $this->addColumn('{{%partidos}}', 'asistente3', $this->string(100)->null()->after('asistente2'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%partidos}}', 'asistente3');
        $this->dropColumn('{{%partidos}}', 'asistente2');
        $this->dropColumn('{{%partidos}}', 'asistente1');
        $this->dropColumn('{{%partidos}}', 'arbitro');
        $this->addColumn('{{%partidos}}', 'goles_local', $this->tinyInteger()->null()->after('estado'));
        $this->addColumn('{{%partidos}}', 'goles_visitante', $this->tinyInteger()->null()->after('goles_local'));
    }
}
