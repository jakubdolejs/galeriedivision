<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_artist_feature_image extends CI_Migration {
    
    public function up() {
        $this->dbforge->add_column("artist_gallery",array(
            "image_id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>true,
                "null"=>true
            )
        ));
    }
    
    public function down() {
        $this->dbforge->drop_column("artist_gallery","image_id");
    }
}