<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_split_artist_names extends CI_Migration {

    public function up() {
        $this->dbforge->add_column("artist",array(
            "surname"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128,
                "null"=>true
            )
        ));
        $this->load->database();
        $this->db->query("UPDATE artist SET surname = substr(name,locate(' ',name)), name = substring_index(name,' ',1)");
    }

    public function down() {
        $this->load->database();
        $this->db->query("UPDATE artist SET name = concat(name,' ',surname)");

        $this->dbforge->drop_column("artist","surname");
    }
}