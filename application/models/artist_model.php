<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Artist_model extends CI_Model {
    
    function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function get_artists($gallery_id=null) {
        $this->db->select("artist.id, artist.name, gallery_id, represented, image_id")
                ->from("artist");
        if ($gallery_id) {
            $this->db->join("artist_gallery","artist_gallery.artist_id = artist.id")
                ->where("gallery_id",$gallery_id);
        } else {
            $this->db->join("artist_gallery","artist_gallery.artist_id = artist.id","left");
        }
        $this->db->order_by("represented","desc")
                ->order_by("name");
        $query = $this->db->get();
        if ($query->num_rows()) {
            $artists = array();
            foreach ($query->result_array() as $row) {
                if (!array_key_exists($row["id"],$artists)) {
                    $artists[$row["id"]] = array(
                        "id"=>$row["id"],
                        "name"=>$row["name"],
                        "galleries"=>array()
                    );
                    if ($row["gallery_id"]) {
                        $artists[$row["id"]]["galleries"][$row["gallery_id"]] = array(
                            "represented"=>intval($row["represented"]),
                            "image_id"=>$row["image_id"]
                        );
                    }
                }
            }
            return array_values($artists);
        }
        return null;
    }
}