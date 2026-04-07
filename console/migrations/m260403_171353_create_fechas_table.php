<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%fechas}}`.
 */
class m260403_171353_create_fechas_table extends Migration
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

        $this->createTable('{{%fechas}}', [
            'id'                   => $this->primaryKey(),
            'numero_fecha'         => $this->integer()->notNull(),
            'fecha_programada'     => $this->dateTime()->notNull(),
            'fecha_reprogramada_1' => $this->dateTime()->null(),
            'fecha_reprogramada_2' => $this->dateTime()->null(),
            'fecha_jugada'         => $this->date()->null(),
            'club_local_id'        => $this->integer()->notNull(),
            'club_visitante_id'    => $this->integer()->notNull(),
            'arbitro_id'           => $this->integer()->null(),
            'observaciones'        => $this->text()->null(),
            'created_by'           => $this->integer()->notNull(),
            'updated_by'           => $this->integer()->null(),
            'created_at'           => $this->integer()->notNull(),
            'updated_at'           => $this->integer()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%fechas}}');
    }
}
