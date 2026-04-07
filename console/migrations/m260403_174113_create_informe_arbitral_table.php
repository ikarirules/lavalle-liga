<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%informe_arbitral}}`.
 */
class m260403_174113_create_informe_arbitral_table extends Migration
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

        $this->createTable('{{%informe_arbitral}}', [
            'id'           => $this->primaryKey(),
            'partido_id'   => $this->integer()->notNull(),
            'arbitro_id'   => $this->integer()->notNull(),
            'asistente_id' => $this->integer()->null(),
            'observaciones'=> $this->text()->null(),
            'created_by'   => $this->integer()->notNull(),
            'updated_by'   => $this->integer()->null(),
            'created_at'   => $this->integer()->notNull(),
            'updated_at'   => $this->integer()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%informe_arbitral}}');
    }
}
