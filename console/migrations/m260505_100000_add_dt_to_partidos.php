<?php

use yii\db\Migration;

class m260505_100000_add_dt_to_partidos extends Migration
{
    public function safeUp()
    {
        $this->addColumn('partidos', 'dt1_local',      $this->string(100)->null()->after('club_local_id'));
        $this->addColumn('partidos', 'dt2_local',      $this->string(100)->null()->after('dt1_local'));
        $this->addColumn('partidos', 'dt1_visitante',  $this->string(100)->null()->after('club_visitante_id'));
        $this->addColumn('partidos', 'dt2_visitante',  $this->string(100)->null()->after('dt1_visitante'));
    }

    public function safeDown()
    {
        $this->dropColumn('partidos', 'dt1_local');
        $this->dropColumn('partidos', 'dt2_local');
        $this->dropColumn('partidos', 'dt1_visitante');
        $this->dropColumn('partidos', 'dt2_visitante');
    }
}
