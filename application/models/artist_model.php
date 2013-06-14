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

    public function get_name($artist_id) {
        $this->db->select("name")
            ->from("artist")
            ->where("id",$artist_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row["name"];
        }
        return null;
    }

    public function get_artist($artist_id) {
        $this->db->select("artist.id, artist.name, gallery.id as 'gid', city, artist_id is not null as 'available', represented is not null and represented > 0 as 'represented', image_id")
            ->from("artist")
            ->join("gallery","1","left")
            ->join("artist_gallery","artist_gallery.gallery_id = gallery.id AND artist_gallery.artist_id = artist.id","left")
            ->where("artist.id",$artist_id);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $artist = array();
            foreach ($query->result_array() as $row) {
                if (empty($artist)) {
                    $artist = array(
                        "id"=>$row["id"],
                        "name"=>$row["name"],
                        "galleries"=>array()
                    );
                }
                $artist["galleries"][$row["gid"]] = array(
                    "city"=>$row["city"],
                    "image_id"=>$row["image_id"],
                    "available"=>(bool)$row["available"],
                    "represented"=>(bool)$row["represented"]
                );
            }
            return $artist;
        }
        return null;
    }

    public function update_name($artist_id,$name) {
        $this->db->set("name",$name)
            ->where("id",$artist_id);
        return $this->db->update("artist");
    }

    public function update_gallery_info($artist_id,$gallery_id,$listed,$represented,$image_id) {
        $this->db->trans_start();
        $this->db->where("artist_id",$artist_id);
        $this->db->where("gallery_id",$gallery_id);
        $this->db->delete("artist_gallery");
        if ($listed) {
            $this->db->set("artist_id",$artist_id)
                ->set("gallery_id",$gallery_id)
                ->set("represented",intval($represented))
                ->set("image_id",$image_id);
            $this->db->insert("artist_gallery");
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== false;
    }

    private function artist_id_exists($id) {
        $this->db->where("id",$id);
        $this->db->from("artist");
        return $this->db->count_all_results() > 0;
    }

    public function add($name) {
        $this->load->helper("text");
        $base_id = preg_replace("/[^a-z0-9]+/i","-",strtolower(convert_accented_characters($name)));
        $id = $base_id;
        $i = 1;
        while ($this->artist_id_exists($id)) {
            $id = $base_id."-".$i;
            $i++;
        }
        $this->db->set("id",$id);
        $this->db->set("name",$name);
        if ($this->db->insert("artist")) {
            return $id;
        }
        return false;
    }

    public function is_deletable_by_user($artist_id,$user) {
        $this->db->from("artist_exhibition")
            ->where("artist_id",$artist_id);
        if ($this->db->count_all_results() > 0) {
            return false;
        }
        $this->db->from("news_artist")
            ->where("artist_id",$artist_id);
        if ($this->db->count_all_results() > 0) {
            return false;
        }
        $this->db->from("image_artist")
            ->where("artist_id",$artist_id);
        if ($this->db->count_all_results() > 0) {
            return false;
        }
        if (!$user["superuser"]) {
            $this->db->where("artist_id",$artist_id);
            if (!empty($user["galleries"])) {
                $this->db->where_not_in("gallery_id",$user["galleries"]);
            }
            $this->db->from("artist_gallery");
            if ($this->db->count_all_results() > 0) {
                return false;
            }
        }
        return true;
    }

    public function delete($artist_id) {
        $this->db->trans_start();
        $this->db->where("artist_id",$artist_id);
        $this->db->delete("artist_gallery");
        $this->db->where("id",$artist_id);
        $this->db->delete("artist");
        $this->db->trans_complete();
        return $this->db->trans_status() !== false;
    }
}