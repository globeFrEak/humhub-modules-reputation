<?php

use yii\db\Migration;

class m160403_114257_initial extends Migration {

    public function up() {
        $this->createTable('reputation_user', array(
            'id' => 'pk',
            'value' => 'int(11) NOT NULL',
            'visibility' => 'tinyint(4) NOT NULL',
            'user_id' => 'int(11) NOT NULL',
            'space_id' => 'int(11) NOT NULL',
            'wall_id' => 'int(11) NOT NULL',
            'created_at' => 'datetime DEFAULT NULL',
            'created_by' => 'int(11) DEFAULT NULL',
            'updated_at' => 'datetime DEFAULT NULL',
            'updated_by' => 'int(11) DEFAULT NULL',
                ), '');
    }

    public function down() {
        echo "m160403_114257_initial cannot be reverted.\n";

        return false;
    }

    /*
      // Use safeUp/safeDown to run migration code within a transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
