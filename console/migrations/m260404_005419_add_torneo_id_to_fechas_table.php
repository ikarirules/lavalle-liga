<?php

use yii\db\Migration;

class m260404_005419_add_torneo_id_to_fechas_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%fechas}}', 'torneo_id', $this->integer()->null()->after('numero_fecha'));
    }

    public function safeDown()
    {
        $this->dropColumn('{{%fechas}}', 'torneo_id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260404_005419_add_torneo_id_to_fechas_table cannot be reverted.\n";

        return false;
    }
    */
}
