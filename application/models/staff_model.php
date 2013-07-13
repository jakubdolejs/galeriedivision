<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Staff_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->driver("cache");
    }

    public function get_staff($gallery_ids=null) {
        $this->db->select("gallery_staff.id, gallery_id, city, gallery_staff.name, email, priority, lang, gallery_staff_translation.title")
            ->from("gallery_staff")
            ->join("gallery","gallery_id = gallery.id")
            ->join("gallery_staff_translation","gallery_staff_translation.staff_id = gallery_staff.id","left");
        if (!empty($gallery_ids)) {
            $this->db->where_in("gallery_id",$gallery_ids);
        } else {
            $this->db->order_by("gallery_id");
        }
        $this->db->order_by("priority");
        $query = $this->db->get();
        $staff = array();
        foreach ($query->result_array() as $row) {
            if (!array_key_exists($row["id"],$staff)) {
                $staff[$row["id"]] = array(
                    "id"=>$row["id"],
                    "name"=>$row["name"],
                    "gallery"=>array(
                        "id"=>$row["gallery_id"],
                        "city"=>$row["city"]
                    ),
                    "email"=>$row["email"],
                    "priority"=>$row["priority"],
                    "title"=>array()
                );
            }
            $staff[$row["id"]]["title"][$row["lang"]] = $row["title"];
        }
        return array_values($staff);
    }

    public function get_staff_by_id($id) {
        $this->db->select("gallery_staff.id, gallery_id, city, gallery_staff.name, email, priority, lang, gallery_staff_translation.title")
            ->from("gallery_staff")
            ->join("gallery","gallery_id = gallery.id")
            ->join("gallery_staff_translation","gallery_staff_translation.staff_id = gallery_staff.id","left")
            ->where("gallery_staff.id",$id);
        $query = $this->db->get();
        $staff = array();
        foreach ($query->result_array() as $row) {
            if (empty($staff)) {
                $staff = array(
                    "id"=>$row["id"],
                    "name"=>$row["name"],
                    "gallery"=>array(
                        "id"=>$row["gallery_id"],
                        "city"=>$row["city"]
                    ),
                    "email"=>$row["email"],
                    "priority"=>$row["priority"],
                    "title"=>array()
                );
            }
            $staff["title"][$row["lang"]] = $row["title"];
        }
        return $staff;
    }

    public function edit($id,$name,$email,$gallery_id,$titles) {
        $this->db->trans_start();
        $this->db->set("name",$name)
            ->set("email",$email)
            ->set("gallery_id",$gallery_id)
            ->where("id",$id);
        $this->db->update("gallery_staff");
        $this->db->where("staff_id",$id);
        $this->db->delete("gallery_staff_translation");
        $batch = array();
        if (!empty($titles)) {
            foreach ($titles as $lang=>$title) {
                $batch[] = array("staff_id"=>$id,"lang"=>$lang,"title"=>$title);
            }
        }
        if (!empty($batch)) {
            $this->db->insert_batch("gallery_staff_translation",$batch);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() !== false) {
            $this->cache->memcached->clean();
            return true;
        }
        return false;
    }

    public function add($name,$email,$gallery_id,$titles) {
        $this->db->trans_start();
        $this->db->set("name",$name)
            ->set("email",$email)
            ->set("gallery_id",$gallery_id);
        $this->db->insert("gallery_staff");
        $id = $this->db->insert_id();
        $batch = array();
        if (!empty($titles)) {
            foreach ($titles as $lang=>$title) {
                $batch[] = array("staff_id"=>$id,"lang"=>$lang,"title"=>$title);
            }
        }
        if (!empty($batch)) {
            $this->db->insert_batch("gallery_staff_translation",$batch);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() !== false) {
            $this->cache->memcached->clean();
            return true;
        }
        return false;
    }

    public function delete($id) {
        $this->db->from("gallery_staff_translation")
            ->where("staff_id",$id);
        $this->db->delete();
        $this->db->from("gallery_staff")
            ->where("id",$id);
        $this->db->delete();
        $this->cache->memcached->clean();
    }

    public function reorder($priority) {
        $this->db->trans_start();
        foreach ($priority as $gallery_id=>$ids) {
            $i = 1;
            foreach ($ids as $id) {
                $this->db->set("priority",$i)
                    ->where("id",$id);
                $this->db->update("gallery_staff");
                $i++;
            }
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() !== false) {
            $this->cache->memcached->clean();
            return true;
        }
        return false;
    }
}