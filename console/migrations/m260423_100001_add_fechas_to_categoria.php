<?php

use yii\db\Migration;

class m260423_100001_add_fechas_to_categoria extends Migration
{
    public function safeUp()
    {
        $this->addColumn('{{%categoria}}', 'fecha_desde', $this->date()->null()->after('activo'));
        $this->addColumn('{{%categoria}}', 'fecha_hasta', $this->date()->null()->after('fecha_desde'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%categoria}}', 'fecha_hasta');
        $this->dropColumn('{{%categoria}}', 'fecha_desde');
    }
}
