<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%jugador}}`.
 */
class m260414_120000_create_jugador_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%jugador}}', [
            'id'                      => $this->primaryKey(),
            'nombre'                  => $this->string(255)->notNull(),
            'dni'                     => $this->string(20)->notNull()->unique(),
            'categoria_id'            => $this->string(20)->notNull(),
            'club_id'                 => $this->integer()->notNull(),
            'numero_fecha_suspension' => $this->integer()->null(),
            'cant_fechas_suspension'  => $this->integer()->notNull()->defaultValue(0),
            'created_at'              => $this->integer()->notNull(),
            'updated_at'              => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-jugador-club_id', '{{%jugador}}', 'club_id');
        $this->createIndex('idx-jugador-categoria_id', '{{%jugador}}', 'categoria_id');

        $this->addForeignKey(
            'fk-jugador-club_id',
            '{{%jugador}}', 'club_id',
            '{{%club}}', 'id',
            'RESTRICT', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-jugador-club_id', '{{%jugador}}');
        $this->dropIndex('idx-jugador-club_id', '{{%jugador}}');
        $this->dropIndex('idx-jugador-categoria_id', '{{%jugador}}');
        $this->dropTable('{{%jugador}}');
    }
}
