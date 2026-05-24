<?php

use yii\db\Migration;

class m260517_100000_create_informe_gol_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%informe_gol}}', [
            'id'         => $this->primaryKey(),
            'informe_id' => $this->integer()->notNull(),
            'jugador_id' => $this->integer()->notNull(),
            'club_id'    => $this->integer()->notNull(),
            'cantidad'   => $this->tinyInteger()->unsigned()->notNull()->defaultValue(1),
        ], $tableOptions);

        $this->addForeignKey('fk_informe_gol_informe', '{{%informe_gol}}', 'informe_id', '{{%informe_arbitral}}', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_informe_gol_jugador', '{{%informe_gol}}', 'jugador_id', 'jugador', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey('fk_informe_gol_club',    '{{%informe_gol}}', 'club_id',    'club',    'id', 'CASCADE', 'CASCADE');
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_informe_gol_club',    '{{%informe_gol}}');
        $this->dropForeignKey('fk_informe_gol_jugador', '{{%informe_gol}}');
        $this->dropForeignKey('fk_informe_gol_informe', '{{%informe_gol}}');
        $this->dropTable('{{%informe_gol}}');
    }
}
