<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_visit_tracking extends CI_Migration {

    public function up() {
        $this->dbforge->add_field(array(
            "id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>true,
                "null"=>false,
                "auto_increment"=>true
            ),
            "time"=>array(
                "type"=>"DATETIME"
            ),
            "email"=>array(
                "type"=>"VARCHAR",
                "constraint"=>"255"
            ),
            "name"=>array(
                "type"=>"VARCHAR",
                "constraint"=>"128"
            ),
            "gallery_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>"64"
            ),
            "image_id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>TRUE,
            )
        ));
        $this->dbforge->add_key("id",true);
        $this->dbforge->add_key("email",false);
        $this->dbforge->add_key("gallery_id",false);
        $this->dbforge->add_key("image_id",false);
        $this->dbforge->create_table("artist_page_visits");

        $this->load->database();
        $this->db->query("ALTER TABLE artist_page_visits ADD FOREIGN KEY gallery_id_fk (gallery_id) REFERENCES gallery (id) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE artist_page_visits ADD FOREIGN KEY image_id_fk (image_id) REFERENCES image (id) ON DELETE CASCADE ON UPDATE CASCADE");
    }

    public function down() {
        $this->dbforge->drop_table("artist_page_visits");
    }
}