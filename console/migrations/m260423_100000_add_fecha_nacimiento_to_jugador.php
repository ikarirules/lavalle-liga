<?php

use yii\db\Migration;

class m260423_100000_add_fecha_nacimiento_to_jugador extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%jugador}}', 'fecha_nacimiento', $this->date()->null()->after('dni'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%jugador}}', 'fecha_nacimiento');
    }
}
