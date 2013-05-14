<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Admin_login_model extends CI_Model {

    function __construct() {
        $this->load->database();
    }

    public function get_user_by_email($email) {
        $this->db->where("user.id",strtolower($email));
        $this->db->join("user_gallery","user_gallery.user_id = user.id","left");
        $query = $this->db->get("user");
        if ($query->num_rows()) {
            return $query->row_array();
        }
        return null;
    }

    public function issue_access_token($user_id,$user_agent,$ip_address) {
        $query = $this->db->get_where("access_token",array("user_id"=>$user_id,"user_agent"=>$user_agent,"ip_address"=>$ip_address));
        $token = md5(uniqid());
        if ($query->num_rows() == 0) {
            $this->db->set("token",$token);
            $this->db->set("user_id",$user_id);
            $this->db->set("user_agent",$user_agent);
            $this->db->set("ip_address",$ip_address);
            $this->db->set("issue_date",'NOW()',false);
            $this->db->set("last_access_date",'NOW()',false);
            $this->db->insert("access_token");
        } else {
            $row = $query->row_array();
            $old_token = $row["token"];
            $this->db->set("issue_date", 'NOW()',false);
            $this->db->set("last_access_date", 'NOW()', false);
            $this->db->set("token",$token);
            $this->db->where("token",$old_token);
            $this->db->update("access_token");
        }
        return $token;
    }

    public function get_user_by_token($token,$user_agent,$ip_address) {
        $this->db->select("user.id, user.superuser, user_gallery.gallery_id")
            ->from("user")
            ->join("user_gallery","user_gallery.user_id = user.id","left")
            ->join("access_token","access_token.user_id = user.id")
            ->where("token",$token)
            ->where("user_agent",substr($user_agent,0,64))
            ->where("ip_address",$ip_address);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $user = null;
            foreach ($query->result_array() as $row) {
                if (empty($user)) {
                    $user = array("id"=>$row["id"],"superuser"=>$row["superuser"],"galleries"=>array());
                }
                $user["galleries"][] = $row["gallery_id"];
            }
            return $user;
        }
        return false;
    }

    public function delete_access_token($token) {
        $this->db->where("token",$token);
        $this->db->delete("access_token");
    }
}