<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_image_version extends CI_Migration {

    public function up() {
        $this->dbforge->add_column("image",array(
            "version"=>array(
                "type"=>"INT",
                "unsigned"=>true,
                "null"=>false,
                "default"=>0
            )
        ));
    }

    public function down() {
        $this->dbforge->drop_column("image","version");
    }
}