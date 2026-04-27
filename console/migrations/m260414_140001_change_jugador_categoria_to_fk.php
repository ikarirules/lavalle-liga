<?php

use yii\db\Migration;

/**
 * Cambia jugador.categoria_id de string a INT FK → categoria.
 */
class m260414_140001_change_jugador_categoria_to_fk extends Migration
{
    public function safeUp()
    {
        // Obtener id de 'Primera' para usarlo como default en filas existentes
        $primeraId = (int) $this->db->createCommand(
            "SELECT id FROM categoria WHERE nombre = 'Primera' LIMIT 1"
        )->queryScalar();

        // Resetear valores actuales al id de Primera (eran strings temporales)
        $this->update('jugador', ['categoria_id' => (string) $primeraId]);

        // Cambiar columna a integer
        $this->alterColumn('{{%jugador}}', 'categoria_id', $this->integer()->notNull());

        // FK
        $this->addForeignKey(
            'fk-jugador-categoria_id',
            '{{%jugador}}', 'categoria_id',
            '{{%categoria}}', 'id',
            'RESTRICT', 'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-jugador-categoria_id', '{{%jugador}}');
        $this->alterColumn('{{%jugador}}', 'categoria_id', $this->string(20)->notNull());
    }
}
