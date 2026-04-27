<?php

use yii\db\Migration;

class m260426_100000_add_club_pase_to_jugador extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%jugador}}', 'club_pase_id', $this->integer()->null()->after('club_id'));
        $this->addForeignKey(
            'fk_jugador_club_pase',
            '{{%jugador}}',
            'club_pase_id',
            '{{%club}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_jugador_club_pase', '{{%jugador}}');
        $this->dropColumn('{{%jugador}}', 'club_pase_id');
    }
}
