<?php

use yii\db\Migration;

/**
 * Alters table `{{%partidos}}`: removes goles_local/goles_visitante, adds arbitro/asistente1/2/3.
 */
class m260503_120000_alter_partidos_table extends Migration
{
    public function safeUp()
    {
        $schema  = $this->db->schema;
        $columns = array_keys($schema->getTableSchema('partidos')->columns);

        if (in_array('goles_local', $columns))    $this->dropColumn('{{%partidos}}', 'goles_local');
        if (in_array('goles_visitante', $columns)) $this->dropColumn('{{%partidos}}', 'goles_visitante');
        if (!in_array('arbitro', $columns))    $this->addColumn('{{%partidos}}', 'arbitro',    $this->string(100)->null()->after('estado'));
        if (!in_array('asistente1', $columns)) $this->addColumn('{{%partidos}}', 'asistente1', $this->string(100)->null()->after('arbitro'));
        if (!in_array('asistente2', $columns)) $this->addColumn('{{%partidos}}', 'asistente2', $this->string(100)->null()->after('asistente1'));
        if (!in_array('asistente3', $columns)) $this->addColumn('{{%partidos}}', 'asistente3', $this->string(100)->null()->after('asistente2'));
    }

    public function safeDown()
    {
        $schema  = $this->db->schema;
        $columns = array_keys($schema->getTableSchema('partidos')->columns);

        if (in_array('asistente3', $columns)) $this->dropColumn('{{%partidos}}', 'asistente3');
        if (in_array('asistente2', $columns)) $this->dropColumn('{{%partidos}}', 'asistente2');
        if (in_array('asistente1', $columns)) $this->dropColumn('{{%partidos}}', 'asistente1');
        if (in_array('arbitro', $columns))    $this->dropColumn('{{%partidos}}', 'arbitro');
        if (!in_array('goles_local', $columns))     $this->addColumn('{{%partidos}}', 'goles_local',     $this->tinyInteger()->null()->after('estado'));
        if (!in_array('goles_visitante', $columns)) $this->addColumn('{{%partidos}}', 'goles_visitante', $this->tinyInteger()->null()->after('goles_local'));
    }
}
