<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%partidos}}`.
 */
class m260403_174401_create_partidos_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%partidos}}', [
            'id'                => $this->primaryKey(),
            'fecha_id'          => $this->integer()->notNull(),
            'categoria'         => $this->string(50)->notNull(),
            'club_local_id'     => $this->integer()->notNull(),
            'club_visitante_id' => $this->integer()->notNull(),
            'cancha'            => $this->string(100)->null(),
            'estado'            => "ENUM('programada','suspendida','postergada','jugada') NOT NULL DEFAULT 'programada'",
            'goles_local'       => $this->tinyInteger()->null(),
            'goles_visitante'   => $this->tinyInteger()->null(),
            'created_by'        => $this->integer()->notNull(),
            'updated_by'        => $this->integer()->null(),
            'created_at'        => $this->integer()->notNull(),
            'updated_at'        => $this->integer()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%partidos}}');
    }
}
