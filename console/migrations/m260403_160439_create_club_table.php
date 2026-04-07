<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%club}}`.
 */
class m260403_160439_create_club_table extends Migration
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

        $this->createTable('{{%club}}', [
            'id'               => $this->primaryKey(),
            'nombre'           => $this->string(100)->notNull(),
            'razon_social'     => $this->string(150)->notNull(),
            'cuit_cuil'        => $this->string(13)->notNull()->unique(),
            'zona'             => $this->string(100)->notNull(),
            'direccion'        => $this->string(200)->null(),
            'telefono'         => $this->string(20)->null(),
            'email'            => $this->string(100)->null(),
            'presidente'       => $this->string(100)->null(),
            'estadio'          => $this->string(100)->null(),
            'anio_fundacion'   => $this->smallInteger()->null(),
            'logo'             => $this->string(255)->null(),
            'instagram'        => $this->string(100)->null(),
            'facebook'         => $this->string(100)->null(),
            'color_primario'   => $this->string(7)->null()->comment('Hex color, ej: #FF0000'),
            'color_secundario' => $this->string(7)->null()->comment('Hex color, ej: #FFFFFF'),
            'activo'           => $this->tinyInteger(1)->notNull()->defaultValue(1),
            'created_at'       => $this->integer()->notNull(),
            'updated_at'       => $this->integer()->notNull(),
        ], $tableOptions);

        $this->createIndex('idx-club-activo', '{{%club}}', 'activo');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-club-activo', '{{%club}}');
        $this->dropTable('{{%club}}');
    }
}
