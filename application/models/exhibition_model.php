<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Exhibition_model extends CI_Model {
    
    function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function get_exhibitions($type,$lang,$gallery_id=NULL) {
        $this->db->select("exhibition.id, exhibition_translation.title, exhibition_translation.text, exhibition.start_date, exhibition.end_date, exhibition.reception_start, exhibition.reception_end, image_exhibition.image_id, space.name, space.gallery_id, artist.id as 'artist_id', artist.name as 'artist_name'")
                ->from("exhibition")
                ->join("exhibition_translation","exhibition_translation.exhibition_id = exhibition.id")
                ->join("space_exhibition","space_exhibition.exhibition_id = exhibition.id")
                ->join("space","space.id = space_exhibition.space_id")
                ->join("image_exhibition","image_exhibition.exhibition_id = exhibition.id AND image_exhibition.cover_image = 1","left")
                ->join("artist_exhibition","artist_exhibition.exhibition_id = exhibition.id JOIN artist ON (artist_exhibition.artist_id = artist.id)","left")
                ->where("exhibition_translation.lang",$lang);
        if ($gallery_id) {
            $this->db->where("space.gallery_id",$gallery_id);
        } else {
            $this->db->order_by("space.gallery_id");
        }
        if ($type == "current") {
            $this->db->where("end_date >= NOW()",NULL,FALSE)
                    ->where("start_date <= NOW()",NULL,FALSE)
                    ->order_by("start_date");
        } else if ($type == "upcoming") {
            $this->db->where("start_date > NOW()",NULL,FALSE)
                    ->order_by("start_date");
        } else if ($type == "past") {
            $this->db->where("end_date < NOW()",NULL,FALSE)
                    ->order_by("end_date","DESC");
        }
        $this->db->order_by("priority");
        $this->db->order_by("artist.name");
        $query = $this->db->get();
        $exhibitions = array();
        foreach ($query->result_array() as $exhibition) {
            if (!array_key_exists($exhibition["id"], $exhibitions)) {
                $exhibitions[$exhibition["id"]] = $exhibition;
                if (isset($exhibitions[$exhibition["id"]]["artist_name"])) {
                    unset($exhibitions[$exhibition["id"]]["artist_name"]);
                }
                $exhibitions[$exhibition["id"]]["artists"] = array();
            }
            if (!empty($exhibition["artist_id"])) {
                $exhibitions[$exhibition["id"]]["artists"][$exhibition["artist_id"]] = $exhibition["artist_name"];
            }
        }
        return $exhibitions;
    }
}