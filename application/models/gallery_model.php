<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Gallery_model extends CI_Model {
    
    function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function get_galleries() {
        $this->db->select("id, city");
        $query = $this->db->get("gallery");
        return $query->result_array();
    }
    
    public function get_gallery($id) {
        $query = $this->db->get_where("gallery",array("id"=>$id));
        return $query->row_array();
    }
    
    public function get_gallery_hours($gallery_id) {
        $this->db->order_by("day");
        $this->db->order_by("open_time");
        $query = $this->db->get_where("gallery_hours",array("gallery_id"=>$gallery_id));
        return $query->result_array();
    }
    
    public function get_gallery_staff($gallery_id,$lang="en") {
        $this->db->order_by("priority");
        $this->db->join("gallery_staff_translation","gallery_staff_translation.staff_id = gallery_staff.id");
        $query = $this->db->get_where("gallery_staff",array("gallery_id"=>$gallery_id,"gallery_staff_translation.lang"=>$lang));
        return $query->result_array();
    }
}