<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_news extends CI_Migration {
    
    public function up() {
        $this->load->database();
        
        $this->dbforge->add_field(array(
            "id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>TRUE,
                "auto_increment"=>TRUE
            ),
            "headline"=>array(
                "type"=>"VARCHAR",
                "constraint"=>256
            ),
            "media_outlet"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            ),
            "date_published"=>array(
                "type"=>"DATE"
            ),
            "url"=>array(
                "type"=>"VARCHAR",
                "constraint"=>256
            )
        ));
        $this->dbforge->add_key("id",TRUE);
        $this->dbforge->create_table("news");
        
        $this->dbforge->add_field(array(
            "news_id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>TRUE
            ),
            "lang"=>array(
                "type"=>"CHAR",
                "constraint"=>2
            ),
            "text"=>array(
                "type"=>"TEXT"
            )
        ));
        $this->dbforge->add_key("news_id",TRUE);
        $this->dbforge->add_key("lang",TRUE);
        $this->dbforge->create_table("news_translation");        
        $this->db->query("ALTER TABLE news_translation ADD FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE ON UPDATE CASCADE");
        
        $this->dbforge->add_field(array(
            "news_id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>TRUE
            ),
            "artist_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            )
        ));
        $this->dbforge->add_key("news_id",TRUE);
        $this->dbforge->add_key("artist_id",TRUE);
        $this->dbforge->create_table("news_artist");
        $this->db->query("ALTER TABLE news_artist ADD FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE news_artist ADD FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE ON UPDATE CASCADE");
        
        $this->dbforge->add_field(array(
            "news_id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>TRUE
            ),
            "gallery_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>64
            )
        ));
        $this->dbforge->add_key("news_id",TRUE);
        $this->dbforge->add_key("gallery_id",TRUE);
        $this->dbforge->create_table("news_gallery");
        $this->db->query("ALTER TABLE news_gallery ADD FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE news_gallery ADD FOREIGN KEY (gallery_id) REFERENCES gallery (id) ON DELETE CASCADE ON UPDATE CASCADE");
        
        $this->dbforge->add_field(array(
            "news_id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>TRUE
            ),
            "exhibition_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            )
        ));
        $this->dbforge->add_key("news_id",TRUE);
        $this->dbforge->add_key("exhibition_id",TRUE);
        $this->dbforge->create_table("news_exhibition");
        $this->db->query("ALTER TABLE news_exhibition ADD FOREIGN KEY (news_id) REFERENCES news (id) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE news_exhibition ADD FOREIGN KEY (exhibition_id) REFERENCES exhibition (id) ON DELETE CASCADE ON UPDATE CASCADE");
    }
    
    public function down() {
        $this->db->query("ALTER TABLE news_gallery DROP FOREIGN KEY (news_id)");
        $this->db->query("ALTER TABLE news_gallery DROP FOREIGN KEY (gallery_id)");
        $this->db->query("ALTER TABLE news_exhibition DROP FOREIGN KEY (news_id)");
        $this->db->query("ALTER TABLE news_exhibition DROP FOREIGN KEY (exhibition_id)");
        $this->db->query("ALTER TABLE news_artist DROP FOREIGN KEY (news_id)");
        $this->db->query("ALTER TABLE news_artist DROP FOREIGN KEY (artist_id)");
        $this->db->query("ALTER TABLE image_exhibition DROP FOREIGN KEY (news_id)");
        $this->dbforge->drop_table("news_gallery");
        $this->dbforge->drop_table("news_exhibition");
        $this->dbforge->drop_table("news_artist");
        $this->dbforge->drop_table("news_translation");
        $this->dbforge->drop_table("news");
    }
}