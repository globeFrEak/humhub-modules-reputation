<?php

use yii\db\Migration;

class uninstall extends Migration
{

    public function up()
    {
        $this->dropTable('reputation_user');
        $this->dropTable('reputation_content');        
    }

    public function down()
    {
        echo "uninstall does not support migration down.\n";
        return false;
    }

}