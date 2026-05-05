<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%lista_jugadores}}`.
 */
class m260503_140000_create_lista_jugadores_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%lista_jugadores}}', [
            'id'               => $this->primaryKey(),
            'tipo_lista'       => "ENUM('buena_fe','partido') NOT NULL DEFAULT 'buena_fe'",
            'partido_id'       => $this->integer()->null(),
            'club_id'          => $this->integer()->notNull(),
            'jugador_id'       => $this->integer()->notNull(),
            'remera_local'     => $this->tinyInteger()->null(),
            'remera_visitante' => $this->tinyInteger()->null(),
            'created_by'       => $this->integer()->notNull(),
            'updated_by'       => $this->integer()->null(),
            'created_at'       => $this->integer()->notNull(),
            'updated_at'       => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addForeignKey(
            'fk_lista_jugadores_partido',
            '{{%lista_jugadores}}', 'partido_id',
            '{{%partidos}}', 'id',
            'SET NULL', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_lista_jugadores_club',
            '{{%lista_jugadores}}', 'club_id',
            'club', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_lista_jugadores_jugador',
            '{{%lista_jugadores}}', 'jugador_id',
            'jugador', 'id',
            'CASCADE', 'CASCADE'
        );

        // Un jugador no puede estar dos veces en la misma lista de un mismo partido/club
        $this->createIndex(
            'uq_lista_partido_club_jugador',
            '{{%lista_jugadores}}',
            ['partido_id', 'club_id', 'jugador_id'],
            true
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_lista_jugadores_jugador', '{{%lista_jugadores}}');
        $this->dropForeignKey('fk_lista_jugadores_club', '{{%lista_jugadores}}');
        $this->dropForeignKey('fk_lista_jugadores_partido', '{{%lista_jugadores}}');
        $this->dropTable('{{%lista_jugadores}}');
    }
}
