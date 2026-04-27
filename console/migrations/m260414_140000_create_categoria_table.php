<?php

use yii\db\Migration;

class m260414_140000_create_categoria_table extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%categoria}}', [
            'id'          => $this->primaryKey(),
            'nombre'      => $this->string(50)->notNull()->unique(),
            'permite_bf'  => $this->tinyInteger(1)->notNull()->defaultValue(0),
            'activo'      => $this->tinyInteger(1)->notNull()->defaultValue(1),
            'created_at'  => $this->integer()->notNull(),
            'updated_at'  => $this->integer()->notNull(),
        ], $tableOptions);

        $now = time();

        $this->batchInsert('{{%categoria}}', ['nombre', 'permite_bf', 'activo', 'created_at', 'updated_at'], [
            ['Primera',   0, 1, $now, $now],
            ['Reserva',   0, 1, $now, $now],
            ['Cuarta',    0, 1, $now, $now],
            ['Quinta',    0, 1, $now, $now],
            ['Femenino',  0, 1, $now, $now],
            ['Séptima',   1, 1, $now, $now],
            ['Octava',    1, 1, $now, $now],
            ['Sénior',    1, 1, $now, $now],
            ['Veteranos', 1, 1, $now, $now],
            ['+50',       1, 1, $now, $now],
            ['Directivo', 0, 1, $now, $now],
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('{{%categoria}}');
    }
}
