<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_add_news_image extends CI_Migration {

    public function up() {
        $this->load->database();
        $this->dbforge->add_field(array(
            "news_id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>true,
                "null"=>false
            ),
            "image_id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>true,
                "null"=>false
            )
        ));
        $this->dbforge->add_key("news_id",TRUE);
        $this->dbforge->add_key("image_id",TRUE);
        $this->dbforge->create_table("news_image");
        $this->db->query("ALTER TABLE news_image ADD FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE news_image ADD FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE ON UPDATE CASCADE");
    }

    public function down() {
        $this->load->database();
        $this->db->query("ALTER TABLE news_image DROP FOREIGN KEY image_id");
        $this->db->query("ALTER TABLE news_image DROP FOREIGN KEY news_id");
        $this->dbforge->drop_table("news_image");
    }
}