<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_gallery extends CI_Migration {
    
    function __construct($config = array()) {
        parent::__construct($config);        
        $this->load->database();
    }
    
    public function up() {
        // Galleries
        $this->dbforge->add_field(array(
            "id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>"64"
            ),
            "address"=>array(
                "type"=>"VARCHAR",
                "constraint"=>"128"
            ),
            "city"=>array(
                "type"=>"VARCHAR",
                "constraint"=>"128"
            ),
            "province"=>array(
                "type"=>"CHAR",
                "constraint"=>"2"
            ),
            "postal_code"=>array(
                "type"=>"VARCHAR",
                "constraint"=>"16"
            ),
            "latitude"=>array(
                "type"=>"DOUBLE"
            ),
            "longitude"=>array(
                "type"=>"DOUBLE"
            ),
            "telephone"=>array(
                "type"=>"VARCHAR",
                "constraint"=>"32"
            )
        ));
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->create_table("gallery");
        
        // Gallery hours
        $this->dbforge->add_field(array(
            "gallery_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>"64"
            ),
            "day"=>array(
                "type"=>"TINYINT",
                "constraint"=>1
            ),
            "open_time"=>array(
                "type"=>"TIME"
            ),
            "close_time"=>array(
                "type"=>"TIME"
            )
        ));
        $this->dbforge->add_key("gallery_id", TRUE);
        $this->dbforge->add_key("day", TRUE);
        $this->dbforge->add_key("open_time", TRUE);
        $this->dbforge->add_key("close_time", TRUE);
        $this->dbforge->create_table("gallery_hours");
        $this->db->query("ALTER TABLE gallery_hours ADD FOREIGN KEY (gallery_id) REFERENCES gallery (id) ON DELETE CASCADE ON UPDATE CASCADE");
        
        // Gallery staff
        $this->dbforge->add_field(array(
            "id"=>array(
                "type"=>"INT",
                "auto_increment"=>TRUE
            ),
            "gallery_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>64
            ),
            "name"=>array(
                "type"=>"VARCHAR",
                "constraint"=>"128"
            ),
            "email"=>array(
                "type"=>"VARCHAR",
                "constraint"=>"256"
            ),
            // If priority is negative the person will not be listed on the website
            "priority"=>array(
                "type"=>"INT",
                "constraint"=>4
            ),
            "password_checksum"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128,
                "null"=>TRUE
            )
        ));
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->create_table("gallery_staff");
        $this->db->query("ALTER TABLE gallery_staff ADD FOREIGN KEY (gallery_id) REFERENCES gallery (id) ON DELETE CASCADE ON UPDATE CASCADE");
        
        // Staff translation
        $this->dbforge->add_field(array(
            "staff_id"=>array(
                "type"=>"INT",
                "auto_increment"=>TRUE
            ),
            "lang"=>array(
                "type"=>"CHAR",
                "constraint"=>2
            ),
            "title"=>array(
                "type"=>"VARCHAR",
                "constraint"=>"128"
            )
        ));
        $this->dbforge->add_key("staff_id", TRUE);
        $this->dbforge->add_key("lang", TRUE);
        $this->dbforge->create_table("gallery_staff_translation");
        $this->db->query("ALTER TABLE gallery_staff_translation ADD FOREIGN KEY (staff_id) REFERENCES gallery_staff (id) ON DELETE CASCADE ON UPDATE CASCADE");
        
        $this->db->insert_batch("gallery",array(
            array(
                "id"=>"montreal",
                "address"=>"2020 rue William",
                "city"=>"Montreal",
                "province"=>"QC",
                "postal_code"=>"H3J 1R8",
                "telephone"=>"514 938 3863",
                "latitude"=>"45.48582",
                "longitude"=>"-73.56853"
            ),
            array(
                "id"=>"toronto",
                "address"=>"45 Ernest Avenue",
                "city"=>"Toronto",
                "province"=>"ON",
                "postal_code"=>"M6P 3M7",
                "telephone"=>"647 346 9082",
                "latitude"=>"43.65923",
                "longitude"=>"-79.45132"
            )
        ));
        $this->db->insert_batch("gallery_hours",array(
            array(
                "gallery_id"=>"montreal",
                "day"=>2,
                "open_time"=>"10:00:00",
                "close_time"=>"18:00:00"
            ),
            array(
                "gallery_id"=>"montreal",
                "day"=>3,
                "open_time"=>"10:00:00",
                "close_time"=>"18:00:00"
            ),
            array(
                "gallery_id"=>"montreal",
                "day"=>4,
                "open_time"=>"10:00:00",
                "close_time"=>"18:00:00"
            ),
            array(
                "gallery_id"=>"montreal",
                "day"=>5,
                "open_time"=>"10:00:00",
                "close_time"=>"18:00:00"
            ),
            array(
                "gallery_id"=>"montreal",
                "day"=>6,
                "open_time"=>"10:00:00",
                "close_time"=>"17:00:00"
            ),
            array(
                "gallery_id"=>"toronto",
                "day"=>2,
                "open_time"=>"10:00:00",
                "close_time"=>"18:00:00"
            ),
            array(
                "gallery_id"=>"toronto",
                "day"=>3,
                "open_time"=>"10:00:00",
                "close_time"=>"18:00:00"
            ),
            array(
                "gallery_id"=>"toronto",
                "day"=>4,
                "open_time"=>"10:00:00",
                "close_time"=>"18:00:00"
            ),
            array(
                "gallery_id"=>"toronto",
                "day"=>5,
                "open_time"=>"10:00:00",
                "close_time"=>"18:00:00"
            ),
            array(
                "gallery_id"=>"toronto",
                "day"=>6,
                "open_time"=>"10:00:00",
                "close_time"=>"18:00:00"
            )
        ));
        $this->db->insert("gallery_staff",array(
            "gallery_id"=>"montreal",
            "name"=>"Dominique Toutant",
            "email"=>"dtoutant@galeriedivision.com",
            "priority"=>1
        ));
        $staff_id = $this->db->insert_id();
        $this->db->insert_batch("gallery_staff_translation",array(
            array(
                "staff_id"=>$staff_id,
                "lang"=>"en",
                "title"=>"Director"
            ),
            array(
                "staff_id"=>$staff_id,
                "lang"=>"fr",
                "title"=>"Directeur"
            )
        ));
        $this->db->insert("gallery_staff",array(
            "gallery_id"=>"montreal",
            "name"=>"Anie Deslauriers",
            "email"=>"adeslauriers@galeriedivision.com",
            "priority"=>2
        ));
        $staff_id = $this->db->insert_id();
        $this->db->insert_batch("gallery_staff_translation",array(
            array(
                "staff_id"=>$staff_id,
                "lang"=>"en",
                "title"=>"Directors’ assitant"
            ),
            array(
                "staff_id"=>$staff_id,
                "lang"=>"fr",
                "title"=>"Assistant à la direction"
            )
        ));
        $this->db->insert("gallery_staff",array(
            "gallery_id"=>"montreal",
            "name"=>"Robert Saulnier",
            "email"=>"rsaulnier@galeriedivision.com",
            "priority"=>3
        ));
        $staff_id = $this->db->insert_id();
        $this->db->insert_batch("gallery_staff_translation",array(
            array(
                "staff_id"=>$staff_id,
                "lang"=>"en",
                "title"=>"Directors’ assitant"
            ),
            array(
                "staff_id"=>$staff_id,
                "lang"=>"fr",
                "title"=>"Assistant à la direction"
            )
        ));
        $this->db->insert("gallery_staff",array(
            "gallery_id"=>"toronto",
            "name"=>"Dionne McAffee",
            "email"=>"dmcaffee@galeriedivision.com",
            "priority"=>1
        ));
        $staff_id = $this->db->insert_id();
        $this->db->insert_batch("gallery_staff_translation",array(
            array(
                "staff_id"=>$staff_id,
                "lang"=>"en",
                "title"=>"Co-director"
            ),
            array(
                "staff_id"=>$staff_id,
                "lang"=>"fr",
                "title"=>"Co-directrice"
            )
        ));
        $this->db->insert("gallery_staff",array(
            "gallery_id"=>"toronto",
            "name"=>"Gareth Brown-Jowett",
            "email"=>"gbrown-jowett@galeriedivision.com",
            "priority"=>2
        ));
        $staff_id = $this->db->insert_id();
        $this->db->insert_batch("gallery_staff_translation",array(
            array(
                "staff_id"=>$staff_id,
                "lang"=>"en",
                "title"=>"Co-director"
            ),
            array(
                "staff_id"=>$staff_id,
                "lang"=>"fr",
                "title"=>"Co-directeur"
            )
        ));
    }
    
    public function down() {
        $this->dbforge->drop_table("gallery");
        $this->dbforge->drop_table("gallery_hours");
        $this->dbforge->drop_table("gallery_staff");
        $this->dbforge->drop_table("gallery_staff_translation");
    }
}