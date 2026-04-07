<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%torneo}}`.
 */
class m260404_005418_create_torneo_table extends Migration
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

        $this->createTable('{{%torneo}}', [
            'id'           => $this->primaryKey(),
            'nombre'       => $this->string(100)->notNull(),
            'anio'         => $this->smallInteger()->notNull(),
            'fecha_inicio' => $this->date()->null(),
            'fecha_fin'    => $this->date()->null(),
            'activo'       => $this->tinyInteger(1)->notNull()->defaultValue(1),
            'created_by'   => $this->integer()->notNull(),
            'updated_by'   => $this->integer()->null(),
            'created_at'   => $this->integer()->notNull(),
            'updated_at'   => $this->integer()->notNull(),
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%torneo}}');
    }
}
