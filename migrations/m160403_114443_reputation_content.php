<?php

use yii\db\Migration;

class m160403_114443_reputation_content extends Migration {

    public function up() {
        $this->createTable('reputation_content', array(
            'id' => 'pk',
            'score' => 'int(11) NOT NULL',
            'score_short' => 'float(11) NOT NULL',
            'score_long' => 'float(11) NOT NULL',
            'content_id' => 'int(11) DEFAULT NULL',
            'created_at' => 'datetime DEFAULT NULL',
            'created_by' => 'int(11) DEFAULT NULL',
            'updated_at' => 'datetime DEFAULT NULL',
            'updated_by' => 'int(11) DEFAULT NULL',
                ), '');
    }

    public function down() {
        echo "m160403_114443_reputation_content cannot be reverted.\n";

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
