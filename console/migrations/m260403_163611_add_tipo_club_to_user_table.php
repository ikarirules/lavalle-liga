<?php

use yii\db\Migration;

class m260403_163611_add_tipo_club_to_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'tipo', $this->string(20)->null()->after('email')
            ->comment('jugador | arbitro | directivo | miembro_liga | admin_liga'));

        $this->addColumn('{{%user}}', 'club_id', $this->integer()->null()->after('tipo'));

        $this->addForeignKey(
            'fk-user-club_id',
            '{{%user}}',
            'club_id',
            '{{%club}}',
            'id',
            'SET NULL',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-user-club_id', '{{%user}}');
        $this->dropColumn('{{%user}}', 'club_id');
        $this->dropColumn('{{%user}}', 'tipo');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260403_163611_add_tipo_club_to_user_table cannot be reverted.\n";

        return false;
    }
    */
}
