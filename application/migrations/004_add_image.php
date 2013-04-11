<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_image extends CI_Migration {
    
    public function up() {
        $this->load->database();
        
        // Work
        $this->dbforge->add_field(array(
            "id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>TRUE,
                "auto_increment"=>TRUE
            ),
            "work_width"=>array(
                "type"=>"INT",
                "constraint"=>8,
                "unsigned"=>TRUE
            ),
            "work_height"=>array(
                "type"=>"INT",
                "constraint"=>8,
                "unsigned"=>TRUE
            ),
            "work_depth"=>array(
                "type"=>"INT",
                "constraint"=>8,
                "unsigned"=>TRUE
            ),
            "work_creation_year"=>array(
                "type"=>"VARCHAR",
                "constraint"=>32
            ),
            "image_width"=>array(
                "type"=>"INT",
                "unsigned"=>TRUE,
                "constraint"=>8
            ),
            "image_height"=>array(
                "type"=>"INT",
                "unsigned"=>TRUE,
                "constraint"=>8
            )
        ));
        $this->dbforge->add_key("id",TRUE);
        $this->dbforge->create_table("image");
        
        // Work translation
        $this->dbforge->add_field(array(
            "image_id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>TRUE
            ),
            "lang"=>array(
                "type"=>"CHAR",
                "constraint"=>2
            ),
            "title"=>array(
                "type"=>"VARCHAR",
                "constraint"=>256
            ),
            "description"=>array(
                "type"=>"VARCHAR",
                "constraint"=>256
            )
        ));
        $this->dbforge->add_key("image_id",TRUE);
        $this->dbforge->add_key("lang",TRUE);
        $this->dbforge->create_table("image_translation");
        $this->db->query("ALTER TABLE image_translation ADD FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE ON UPDATE CASCADE");
        
        // Work artist
        $this->dbforge->add_field(array(
            "image_id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>TRUE
            ),
            "artist_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            )
        ));
        $this->dbforge->add_key("image_id",TRUE);
        $this->dbforge->add_key("artist_id",TRUE);
        $this->dbforge->create_table("image_artist");
        $this->db->query("ALTER TABLE image_artist ADD FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE image_artist ADD FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE ON UPDATE CASCADE");
        
        // Work gallery
        $this->dbforge->add_field(array(
            "image_id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>TRUE
            ),
            "gallery_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            ),
            "priority"=>array(
                "type"=>"INT",
                "unsigned"=>TRUE,
                "null"=>FALSE,
                "default"=>0
            )
        ));
        $this->dbforge->add_key("image_id",TRUE);
        $this->dbforge->add_key("gallery_id",TRUE);
        $this->dbforge->create_table("image_gallery");
        $this->db->query("ALTER TABLE image_gallery ADD FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE image_gallery ADD FOREIGN KEY (gallery_id) REFERENCES gallery (id) ON DELETE CASCADE ON UPDATE CASCADE");
        
        
        // Exhibition image link
        $this->dbforge->add_field(array(
            "image_id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>TRUE
            ),
            "exhibition_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            ),
            "cover_image"=>array(
                "type"=>"TINYINT",
                "unsigned"=>TRUE,
                "constraint"=>1,
                "null"=>FALSE
            )
        ));
        $this->dbforge->add_key("exhibition_id",TRUE);
        $this->dbforge->add_key("image_id",TRUE);
        $this->dbforge->create_table("image_exhibition");
        $this->db->query("ALTER TABLE image_exhibition ADD UNIQUE KEY (exhibition_id,cover_image)");
        $this->db->query("ALTER TABLE image_exhibition ADD FOREIGN KEY (exhibition_id) REFERENCES exhibition (id) ON DELETE CASCADE ON UPDATE CASCADE");
    }
    
    public function down() {
        $this->db->query("ALTER TABLE image_exhibition DROP FOREIGN KEY (image_id)");
        $this->db->query("ALTER TABLE image_translation DROP FOREIGN KEY (image_id)");
        $this->db->query("ALTER TABLE image_artist DROP FOREIGN KEY (image_id)");
        $this->db->query("ALTER TABLE image_artist DROP FOREIGN KEY (artist_id)");
        $this->db->query("ALTER TABLE image_gallery DROP FOREIGN KEY (image_id)");
        $this->db->query("ALTER TABLE image_gallery DROP FOREIGN KEY (gallery_id)");
        $this->dbforge->drop_table("image_exhibition");
        $this->dbforge->drop_table("image_gallery");
        $this->dbforge->drop_table("image_artist");
        $this->dbforge->drop_table("image_translation");
        $this->dbforge->drop_table("image");
    }
}