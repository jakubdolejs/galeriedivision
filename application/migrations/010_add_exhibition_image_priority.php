<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_exhibition_image_priority extends CI_Migration {

    public function up() {
        $this->dbforge->add_column("image_exhibition",array(
            "priority"=>array(
                "type"=>"INT",
                "null"=>false,
                "default"=>0
            )
        ));
    }

    public function down() {
        $this->dbforge->drop_column("image_exhibition","priority");
    }
}