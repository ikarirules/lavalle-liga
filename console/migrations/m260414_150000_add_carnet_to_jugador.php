<?php

use yii\db\Migration;

class m260414_150000_add_carnet_to_jugador extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%jugador}}', 'numero_carnet', $this->string(20)->null()->unique()->after('dni'));
        $this->addColumn('{{%jugador}}', 'foto_carnet',   $this->string(255)->null()->after('numero_carnet'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%jugador}}', 'foto_carnet');
        $this->dropColumn('{{%jugador}}', 'numero_carnet');
    }
}
