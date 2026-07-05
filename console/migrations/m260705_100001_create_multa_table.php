<?php

use yii\db\Migration;

class m260705_100001_create_multa_table extends Migration
{
    public function up()
    {
        $this->createTable('multa', [
            'id'                  => $this->primaryKey(),
            'jugador_id'          => $this->integer()->notNull(),
            'informe_detalle_id'  => $this->integer()->notNull(),
            'monto'               => $this->decimal(10, 2)->notNull()->defaultValue(0),
            'pagado'              => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'fecha_pago'          => $this->date()->null(),
            'observaciones'       => $this->string(255)->null(),
            'created_at'          => $this->integer()->null(),
            'updated_at'          => $this->integer()->null(),
            'created_by'          => $this->integer()->null(),
            'updated_by'          => $this->integer()->null(),
        ]);

        $this->addForeignKey('fk_multa_jugador',         'multa', 'jugador_id',         'jugador',         'id', 'RESTRICT', 'CASCADE');
        $this->addForeignKey('fk_multa_informe_detalle', 'multa', 'informe_detalle_id', 'informe_detalle', 'id', 'RESTRICT', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('fk_multa_informe_detalle', 'multa');
        $this->dropForeignKey('fk_multa_jugador',         'multa');
        $this->dropTable('multa');
    }
}
