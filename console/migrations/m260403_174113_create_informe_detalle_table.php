<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%informe_detalle}}`.
 */
class m260403_174113_create_informe_detalle_table extends Migration
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

        $this->createTable('{{%informe_detalle}}', [
            'id'                 => $this->primaryKey(),
            'informe_id'         => $this->integer()->notNull(),
            'minuto'             => $this->tinyInteger()->null(),
            'jugador_id'         => $this->integer()->null(),
            'numero_camiseta'    => $this->tinyInteger()->null(),
            'club_id'            => $this->integer()->notNull(),
            'tipo_infraccion_id' => $this->integer()->notNull(),
            'created_at'         => $this->integer()->notNull(),
            'updated_at'         => $this->integer()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%informe_detalle}}');
    }
}
