<?php

use yii\db\Migration;

class m170212_111222_adjustements_humhub_1_2_x extends Migration {

    public function up() {
        $this->renameColumn('reputation_user', 'wall_id', 'contentcontainer_id');        
    }

    public function down() {
        echo "m170212_111222_adjustements_humhub_1_2_x cannot be reverted.\n";

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
