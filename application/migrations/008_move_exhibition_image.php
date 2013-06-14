<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Move_exhibition_image extends CI_Migration {

    public function up() {
        $this->load->database();

        $this->db->select("exhibition_id,image_id")
            ->from("image_exhibition")
            ->where("cover_image",1);
        $query = $this->db->get();
        $cover_images = array();
        if ($query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $cover_images[$row["exhibition_id"]] = $row["image_id"];
            }
        }

        $this->db->query("ALTER TABLE image_exhibition DROP index `exhibition_id`");
        $this->dbforge->drop_column("image_exhibition","cover_image");

        $this->dbforge->add_column("exhibition",array(
            "image_id"=>array(
                "type"=>"BIGINT",
                "unsigned"=>true,
                "null"=>true
            )
        ));

        if (count($cover_images)) {
            foreach ($cover_images as $exhibition_id=>$image_id) {
                $this->db->set("image_id",$image_id)
                    ->where("id",$exhibition_id);
                $this->db->update("exhibition");
            }
        }
    }

    public function down() {
        $this->db->select("id","image_id")
            ->from("exhibition")
            ->where("image_id IS NOT NULL",null,false);
        $query = $this->db->get();
        $cover_images = array();
        if ($query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $cover_images[$row["id"]] = $row["image_id"];
            }
        }
        $this->dbforge->drop_column("exhibition","image_id");
        $this->dbforge->add_column("image_exhibition",array(
            "cover_image"=>array(
                "type"=>"TINYINT",
                "constraint"=>1,
                "default"=>0
            )
        ));
        if (count($cover_images)) {
            foreach ($cover_images as $exhibition_id=>$image_id) {
                $this->db->set("cover_image",1)
                    ->where("exhibition_id",$exhibition_id)
                    ->where("image_id",$image_id);
                $this->db->update("image_exhibition");
            }
        }
    }
}