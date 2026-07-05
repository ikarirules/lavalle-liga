<?php

use yii\db\Migration;

class m260705_100000_add_multa_fields_to_tipo_infraccion extends Migration
{
    public function up()
    {
        $this->addColumn('tipo_infraccion', 'genera_multa', $this->tinyInteger(1)->notNull()->defaultValue(0)->after('sancion_fechas_max'));
        $this->addColumn('tipo_infraccion', 'monto_multa',  $this->decimal(10, 2)->null()->after('genera_multa'));
    }

    public function down()
    {
        $this->dropColumn('tipo_infraccion', 'monto_multa');
        $this->dropColumn('tipo_infraccion', 'genera_multa');
    }
}
