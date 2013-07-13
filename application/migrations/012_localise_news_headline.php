<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_localise_news_headline extends CI_Migration {

    public function up() {
        $this->dbforge->add_column("news_translation",array(
            "headline"=>array(
                "type"=>"VARCHAR",
                "constraint"=>256
            )
        ));
        $this->dbforge->modify_column("news",array(
            "media_outlet"=>array(
                "name"=>"source",
                "type"=>"VARCHAR",
                "constraint"=>128
            )
        ));
        $this->load->database();
        $this->db->select("id, headline")->from("news");
        $query = $this->db->get();
        if ($query->num_rows()) {
            $batch = array();
            foreach ($query->result_array() as $row) {
                $this->db->select("lang")->from("news_translation")->where("news_id",$row["id"]);
                $q = $this->db->get();
                if ($q->num_rows()) {
                    $this->db->set("headline",$row["headline"])
                        ->where("news_id",$row["id"]);
                    $this->db->update("news_translation");
                } else {
                    $batch[] = array(
                        "lang"=>"en",
                        "news_id"=>$row["id"],
                        "headline"=>$row["headline"]
                    );
                    $batch[] = array(
                        "lang"=>"fr",
                        "news_id"=>$row["id"],
                        "headline"=>$row["headline"]
                    );
                }
            }
            if (!empty($batch)) {
                $this->db->insert_batch("news_translation",$batch);
            }
        }
        $this->dbforge->drop_column("news","headline");
    }

    public function down() {
        $this->dbforge->add_column("news",array(
            "headline"=>array(
                "type"=>"VARCHAR",
                "constraint"=>256
            )
        ));
        $this->load->database();
        $headlines = array();
        $this->db->select("news_id, headline")->from("news_translation");
        $query = $this->db->get();
        if ($query->num_rows()) {
            foreach ($query->result_array() as $row) {
                if (!isset($headlines[$row["news_id"]])) {
                    $headlines[$row["news_id"]] = array();
                }
                $headlines[$row["news_id"]][] = $row["headline"];
            }
            foreach ($headlines as $id=>$headline) {
                $headlines[$id] = join("/",array_unique($headline));
                $this->db->set("headline",$headline)
                    ->where("id",$id);
                $this->db->update("news");
            }
        }
        $this->dbforge->drop_column("news_translation","headline");
        $this->dbforge->modify_column("news",array(
            "source"=>array(
                "name"=>"media_outlet",
                "type"=>"VARCHAR",
                "constraint"=>128
            )
        ));
    }
}