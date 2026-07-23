<?php

use yii\db\Migration;

class m260723_100000_add_sancion_levantada_to_informe_detalle extends Migration
{
    public function up()
    {
        $this->addColumn('informe_detalle', 'sancion_levantada', $this->tinyInteger(1)->notNull()->defaultValue(0)->after('tipo_infraccion_id'));
    }

    public function down()
    {
        $this->dropColumn('informe_detalle', 'sancion_levantada');
    }
}
