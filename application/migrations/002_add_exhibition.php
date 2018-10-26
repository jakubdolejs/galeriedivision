<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_exhibition extends CI_Migration {
    
    public function up() {
        $this->load->database();
        
        // Exhibitions
        $this->dbforge->add_field(array(
            "id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128                
            ),            
            "start_date"=>array(
                "type"=>"DATE"
            ),
            "end_date"=>array(
                "type"=>"DATE"
            ),
            "reception_start"=>array(
                "type"=>"DATETIME"
            ),
            "reception_end"=>array(
                "type"=>"DATETIME"
            )
        ));
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->create_table("exhibition");
        
        $this->dbforge->add_field(array(
            "exhibition_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            ),
            "title"=>array(
                "type"=>"VARCHAR",
                "constraint"=>256
            ),
            "text"=>array(
                "type"=>"TEXT",
                "null"=>true
            ),
            "lang"=>array(
                "type"=>"CHAR",
                "constraint"=>2
            )
        ));
        $this->dbforge->add_key("exhibition_id", TRUE);
        $this->dbforge->add_key("lang", TRUE);
        $this->dbforge->create_table("exhibition_translation");
        $this->db->query("ALTER TABLE exhibition_translation ADD FOREIGN KEY (exhibition_id) REFERENCES exhibition (id) ON DELETE CASCADE ON UPDATE CASCADE");
        
        // Spaces
        $this->dbforge->add_field(array(
            "id"=>array(
                "type"=>"INT",
                "constraint"=>2,
                "unsigned"=>TRUE,
                "auto_increment"=>TRUE
            ),
            "name"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            ),
            "gallery_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>64
            )
        ));
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->create_table("space");
        $this->db->query("ALTER TABLE space ADD FOREIGN KEY (gallery_id) REFERENCES gallery (id) ON DELETE CASCADE ON UPDATE CASCADE");
        
        // Exhibition space link
        $this->dbforge->add_field(array(
            "space_id"=>array(
                "type"=>"INT",
                "constraint"=>2,
                "unsigned"=>TRUE
            ),
            "exhibition_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            ),
            "priority"=>array(
                "type"=>"INT",
                "constraint"=>2
            )
        ));
        $this->dbforge->add_key("space_id",TRUE);
        $this->dbforge->add_key("exhibition_id",TRUE);
        $this->dbforge->create_table("space_exhibition");
        $this->db->query("ALTER TABLE space_exhibition ADD FOREIGN KEY (space_id) REFERENCES space (id) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE space_exhibition ADD FOREIGN KEY (exhibition_id) REFERENCES exhibition (id) ON DELETE CASCADE ON UPDATE CASCADE");
                
        $this->db->insert("exhibition",array(
            "id"=>"after-the-royal-art-lodge",
            "start_date"=>"2013-03-16",
            "end_date"=>"2013-05-11",
            "reception_start"=>"2013-03-16 14:00:00",
            "reception_end"=>"2013-03-16 17:00:00"
        ));
        $this->db->insert_batch("exhibition_translation",array(
            array(
                "exhibition_id"=>"after-the-royal-art-lodge",
                "title"=>"After the Royal Art Lodge",
                "lang"=>"en"
            ),
            array(
                "exhibition_id"=>"after-the-royal-art-lodge",
                "title"=>"Après le Royal Art Lodge",
                "lang"=>"fr"
            )
        ));
        $this->db->insert("space",array(
            "gallery_id"=>"montreal",
            "name"=>"Main gallery"
        ));
        $space_id = $this->db->insert_id();
        $this->db->insert("space_exhibition",array(
            "space_id"=>$space_id,
            "exhibition_id"=>"after-the-royal-art-lodge",
            "priority"=>1
        ));
    }
    
    public function down() {
        $this->db->query("ALTER TABLE exhibition_translation DROP FOREIGN KEY (exhibition_id)");
        $this->db->query("ALTER TABLE space DROP FOREIGN KEY (gallery_id) REFERENCES gallery (id)");
        $this->db->query("ALTER TABLE space_exhibition DROP FOREIGN KEY (space_id)");
        $this->db->query("ALTER TABLE space_exhibition DROP FOREIGN KEY (exhibition_id)");
        $this->dbforge->drop_table("exhibition");
        $this->dbforge->drop_table("exhibition_translation");
        $this->dbforge->drop_table("space");
        $this->dbforge->drop_table("space_exhibition");
        $this->dbforge->drop_table("exhibition_artist");
    }
}
