<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_access_token extends CI_Migration {

    public function up() {
        $this->load->database();

        $query = $this->db->query("SHOW COLUMNS FROM gallery_staff LIKE 'password_checksum'");
        if ($query->num_rows()) {
            $this->dbforge->drop_column("gallery_staff","password_checksum");
        }

        $this->dbforge->add_field(array(
            "id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            ),
            "password_checksum"=>array(
                "type"=>"VARCHAR",
                "constraint"=>256
            ),
            "superuser"=>array(
                "type"=>"TINYINT",
                "constraint"=>1,
                "default"=>0
            )
        ));
        $this->dbforge->add_key("id",true);
        $this->dbforge->create_table("user");

        $this->dbforge->add_field(array(
            "user_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            ),
            "gallery_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>64
            )
        ));
        $this->dbforge->add_key("user_id",true);
        $this->dbforge->add_key("gallery_id",true);
        $this->dbforge->create_table("user_gallery");

        $this->db->query("ALTER TABLE user_gallery ADD FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE user_gallery ADD FOREIGN KEY (gallery_id) REFERENCES gallery (id) ON DELETE CASCADE ON UPDATE CASCADE");

        $this->dbforge->add_field(array(
            "token"=>array(
                "type"=>"VARCHAR",
                "constraint"=>32
            ),
            "user_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            ),
            "user_agent"=>array(
                "type"=>"VARCHAR",
                "constraint"=>64
            ),
            "ip_address"=>array(
                "type"=>"VARCHAR",
                "constraint"=>32
            ),
            "issue_date"=>array(
                "type"=>"DATETIME"
            ),
            "last_access_date"=>array(
                "type"=>"DATETIME"
            )
        ));
        $this->dbforge->add_key("token",true);
        $this->dbforge->add_key("user_id",true);
        $this->dbforge->add_key("user_agent",true);
        $this->dbforge->add_key("ip_address",true);
        $this->dbforge->create_table("access_token");

        $this->db->query("ALTER TABLE access_token ADD FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE ON UPDATE CASCADE");

        $this->load->helper("bcrypt");
        $this->db->query("INSERT INTO user (id,password_checksum) SELECT email,".$this->db->escape(bcrypt_hash("division"))." FROM gallery_staff");
        $this->db->query("INSERT INTO user_gallery (user_id, gallery_id) SELECT email, gallery_id FROM gallery_staff");

        $this->db->set("id","jakub@sandrafriesen.com")
            ->set("password_checksum",bcrypt_hash("jd9435"))
            ->set("superuser",1);
        $this->db->insert("user");
        $this->db->insert_batch("user_gallery",array(
            array(
                "user_id"=>"jakub@sandrafriesen.com",
                "gallery_id"=>"toronto"
            ),
            array(
                "user_id"=>"jakub@sandrafriesen.com",
                "gallery_id"=>"montreal"
            )
        ));
    }

    public function down() {
        $this->dbforge->drop_table("user_gallery");
        $this->dbforge->drop_table("access_token");
        $this->dbforge->drop_table("user");
    }
}