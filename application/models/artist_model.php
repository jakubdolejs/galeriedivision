<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Artist_model extends CI_Model {
    
    function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function get_artists($gallery_id) {
        $this->db->select("artist.id, artist.name, represented")
                ->from("artist")
                ->join("artist_gallery","artist_gallery.artist_id = artist.id")
                ->where("gallery_id",$gallery_id)
                ->order_by("represented","desc")
                ->order_by("name");
        $query = $this->db->get();
        return $query->result_array();
    }
}