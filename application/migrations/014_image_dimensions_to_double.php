<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_image_dimensions_to_double extends CI_Migration {

    public function up() {
        $this->dbforge->modify_column("image",array(
            "work_width"=>array(
                "type"=>"DOUBLE",
                "constraint"=>"8,2"
            ),
            "work_height"=>array(
                "type"=>"DOUBLE",
                "constraint"=>"8,2"
            ),
            "work_depth"=>array(
                "type"=>"DOUBLE",
                "constraint"=>"8,2"
            )
        ));
    }

    public function down() {
        $this->dbforge->modify_column("image",array(
            "work_width"=>array(
                "type"=>"INT",
                "unsigned"=>true
            ),
            "work_height"=>array(
                "type"=>"INT",
                "unsigned"=>true
            ),
            "work_depth"=>array(
                "type"=>"INT",
                "unsigned"=>true
            )
        ));
    }
}