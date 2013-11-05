<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(rtrim(APPPATH,"/")."/models/GD_Model.php");

class Artist_model extends GD_Model {

    public function get_artists($gallery_id=null) {
        $this->db->select("artist.id, artist.name, artist.surname, gallery_id, represented, image_id")
                ->from("artist");
        if ($gallery_id) {
            $this->db->join("artist_gallery","artist_gallery.artist_id = artist.id")
                ->where("gallery_id",$gallery_id);
        } else {
            $this->db->join("artist_gallery","artist_gallery.artist_id = artist.id","left");
        }
        $this->db->order_by("represented","desc")
                ->order_by("surname, name");
        $query = $this->db->get();
        if ($query->num_rows()) {
            $artists = array();
            foreach ($query->result_array() as $row) {
                if (!array_key_exists($row["id"],$artists)) {
                    $artists[$row["id"]] = array(
                        "id"=>$row["id"],
                        "name"=>$row["name"]." ".$row["surname"],
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

    public function get_all_artist_ids() {
        $query = $this->db->distinct()
            ->select("artist.id, gallery_id")
            ->from("artist")
            ->join("artist_gallery","artist_gallery.artist_id = artist.id")
            ->order_by("surname, name")
            ->get();
        $artists = array();
        foreach ($query->result_array() as $row) {
            if (!array_key_exists($row["gallery_id"],$artists)) {
                $artists[$row["gallery_id"]] = array();
            }
            $artists[$row["gallery_id"]][] = $row["id"];
        }
        return $artists;
    }

    public function get_all_artist_exhibitions() {
        $query = $this->db->distinct()
            ->select("artist_exhibition.artist_id, gallery_id, exhibition_id")
            ->from("artist_exhibition")
            ->join("artist_gallery","artist_gallery.artist_id = artist_exhibition.artist_id")
            ->get();
        $result = array();
        foreach ($query->result_array() as $row) {
            if (!array_key_exists($row["gallery_id"],$result)) {
                $result[$row["gallery_id"]] = array();
            }
            if (!array_key_exists($row["artist_id"],$result[$row["gallery_id"]])) {
                $result[$row["gallery_id"]][$row["artist_id"]] = array();
            }
            $result[$row["gallery_id"]][$row["artist_id"]][] = $row["exhibition_id"];
        }
        return $result;
    }

    public function get_all_artists_with_news() {
        $query = $this->db->distinct()
            ->select("news_artist.artist_id, gallery_id")
            ->from("news_artist")
            ->join("artist_gallery","artist_gallery.artist_id = news_artist.artist_id")
            ->get();
        $result = array();
        foreach ($query->result_array() as $row) {
            if (!array_key_exists($row["gallery_id"],$result)) {
                $result[$row["gallery_id"]] = array();
            }
            $result[$row["gallery_id"]][] = $row["artist_id"];
        }
        return $result;
    }

    public function get_name($artist_id) {
        $this->db->select("name, surname")
            ->from("artist")
            ->where("id",$artist_id);
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row["name"]." ".$row["surname"];
        }
        return null;
    }

    public function get_artist($artist_id) {
        $this->db->select("artist.id, artist.name, artist.surname, gallery.id as 'gid', city, artist_gallery.artist_id is not null as 'available', represented is not null and represented > 0 as 'represented', image_id, cv, lang")
            ->from("artist")
            ->join("artist_translation","artist_translation.artist_id = artist.id","left")
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
                        "name"=>$row["name"]." ".$row["surname"],
                        "first_name"=>$row["name"],
                        "surname"=>$row["surname"],
                        "galleries"=>array()
                    );
                }
                $artist["galleries"][$row["gid"]] = array(
                    "city"=>$row["city"],
                    "image_id"=>$row["image_id"],
                    "available"=>(bool)$row["available"],
                    "represented"=>(bool)$row["represented"]
                );
                if (!empty($row["cv"])) {
                    $artist["cv"][$row["lang"]] = $row["cv"];
                }
            }
            return $artist;
        }
        return null;
    }

    public function update_name($user_id,$artist_id,$name,$surname) {
        if (!$surname) {
            $surname = null;
        }
        $this->db->set("name",$name)
            ->set("surname",$surname)
            ->where("id",$artist_id);
        if ($this->db->update("artist")) {
            $this->cache->memcached->clean();
            $this->log($user_id);
            return true;
        }
        return false;
    }

    public function update_gallery_info($user_id,$artist_id,$gallery_id,$listed,$represented,$image_id) {
        $this->db->trans_start();
        $this->db->where("artist_id",$artist_id);
        $this->db->where("gallery_id",$gallery_id);
        $this->db->delete("artist_gallery");
        $this->log($user_id);
        if ($listed) {
            $this->db->set("artist_id",$artist_id)
                ->set("gallery_id",$gallery_id)
                ->set("represented",intval($represented))
                ->set("image_id",$image_id);
            $this->db->insert("artist_gallery");
            $this->log($user_id);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() !== false) {
            $this->cache->memcached->clean();
            return true;
        }
        return false;
    }

    public function update_cv($user_id,$artist_id,$lang,$cv) {
        $this->db->trans_start();
        $this->db->where("artist_id",$artist_id)
            ->where("lang",$lang);
        $this->db->delete("artist_translation");
        $this->log($user_id);
        $this->db->set("artist_id",$artist_id)
            ->set("lang",$lang)
            ->set("cv",$cv);
        $this->db->insert("artist_translation");
        $this->log($user_id);
        $this->db->trans_complete();
        if ($this->db->trans_status() !== false) {
            $this->cache->memcached->clean();
            return true;
        }
        return false;
    }

    public function get_artist_sections($artist_id,$gallery_id) {
        $this->db->from("image_artist")
            ->join("image_gallery","image_gallery.image_id = image_artist.image_id")
            ->where("artist_id",$artist_id)
            ->where("gallery_id",$gallery_id);
        $has_images = $this->db->count_all_results() > 0;
        $this->db->from("artist_exhibition")
            ->where("artist_id",$artist_id);
        $has_exhibitions = $this->db->count_all_results() > 0;
        $this->db->from("news_artist")
            ->where("artist_id",$artist_id);
        $has_news = $this->db->count_all_results() > 0;
        if (!$has_news && $has_exhibitions) {
            $this->db->from("artist_exhibition")
                ->join("news_exhibition","news_exhibition.exhibition_id = artist_exhibition.exhibition_id")
                ->where("artist_id",$artist_id);
            $has_news = $this->db->count_all_results() > 0;
        }
        $has_cv = false;
        foreach (array("fr","en") as $lang) {
            if (file_exists(rtrim(FCPATH,"/")."/cv_pdf/".$artist_id."-".$lang.".pdf")) {
                $has_cv = true;
                break;
            }
        }
        return array("images"=>$has_images,"exhibitions"=>$has_exhibitions,"news"=>$has_news,"cv"=>$has_cv);
    }

    private function artist_id_exists($id) {
        $this->db->where("id",$id);
        $this->db->from("artist");
        return $this->db->count_all_results() > 0;
    }

    public function add($user_id,$name,$surname) {
        $this->load->helper("text");
        $base_id = preg_replace("/[^a-z0-9]+/i","-",strtolower(convert_accented_characters($name."-".$surname)));
        $id = $base_id;
        $i = 1;
        while ($this->artist_id_exists($id)) {
            $id = $base_id."-".$i;
            $i++;
        }
        $this->db->set("id",$id);
        $this->db->set("name",$name);
        $this->db->set("surname",$surname);
        if ($this->db->insert("artist")) {
            $this->log($user_id);
            $this->cache->memcached->clean();
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

    public function delete($user_id,$artist_id) {
        $this->db->trans_start();
        $this->db->where("artist_id",$artist_id);
        $this->db->delete("artist_gallery");
        $this->log($user_id);
        $this->db->where("id",$artist_id);
        $this->db->delete("artist");
        $this->log($user_id);
        $this->db->trans_complete();
        if ($this->db->trans_status() !== false) {
            $this->cache->memcached->clean();
            return true;
        }
        return false;
    }
}