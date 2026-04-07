<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%tipo_infraccion}}`.
 */
class m260403_174113_create_tipo_infraccion_table extends Migration
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

        $this->createTable('{{%tipo_infraccion}}', [
            'id'                  => $this->primaryKey(),
            'nombre'              => $this->string(100)->notNull(),
            'descripcion'         => $this->text()->null(),
            'sancion_descripcion' => $this->string(100)->null(),
            'sancion_fechas_min'  => $this->tinyInteger()->null(),
            'sancion_fechas_max'  => $this->tinyInteger()->null(),
        ], $tableOptions);

        // Cargar catalogo inicial
        $this->batchInsert('{{%tipo_infraccion}}',
            ['nombre', 'descripcion', 'sancion_descripcion', 'sancion_fechas_min', 'sancion_fechas_max'],
            [
                ['Tarjeta Amarilla',    'Conducta antideportiva, protesta, falta reiterada.',          'Amonestación',              0, 0],
                ['Doble Amarilla',      'Dos amonestaciones en el mismo partido.',                      'Suspensión 1 fecha',         1, 1],
                ['Tarjeta Roja Directa','Falta grave o conducta antideportiva grave.',                  'Suspensión 1 a 3 fechas',    1, 3],
                ['Juego Brusco Grave',  'Entrada peligrosa poniendo en riesgo al rival.',               'Suspensión 3 a 6 fechas',    3, 6],
                ['Insulto a rival',     'Lenguaje ofensivo hacia otro jugador.',                        'Suspensión 2 a 4 fechas',    2, 4],
                ['Insulto al árbitro',  'Lenguaje ofensivo o amenazas al árbitro.',                     'Suspensión 4 a 6 fechas',    4, 6],
                ['Intento de agresión', 'Intención clara de agredir físicamente.',                      'Suspensión 6 a 10 fechas',   6, 10],
                ['Agresión física',     'Golpe, empujón violento, patada.',                             'Suspensión severa o expulsión', 10, null],
                ['Abandono del campo',  'Retiro sin autorización.',                                     'Suspensión 1 a 3 fechas',    1, 3],
                ['Ingreso indebido',    'Ingreso sin autorización al campo de juego.',                  'Suspensión o multa',          1, null],
            ]
        );
    }

    public function safeDown()
    {
        $this->dropTable('{{%tipo_infraccion}}');
    }
}
