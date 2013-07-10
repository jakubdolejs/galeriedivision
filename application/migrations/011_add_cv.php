<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_cv extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(array(
            "artist_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            ),
            "lang"=>array(
                "type"=>"CHAR",
                "constraint"=>2
            ),
            "cv"=>array(
                "type"=>"TEXT"
            )
        ));
        $this->dbforge->add_key("artist_id",true);
        $this->dbforge->add_key("lang",true);
        $this->dbforge->create_table("artist_translation");
        $this->load->database();
        $this->db->query("ALTER TABLE artist_translation ADD FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE ON UPDATE CASCADE");
    }

    public function down() {
        $this->load->database();
        $this->db->query("ALTER TABLE artist_translation DROP FOREIGN KEY (artist_id)");
        $this->dbforge->drop_table("artist_translation");
    }
}