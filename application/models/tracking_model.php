<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(rtrim(APPPATH,"/")."/models/GD_Model.php");

class Tracking_model extends GD_Model {

    public function track_visit($email,$name,$gallery_id,$work_id) {
        $this->db->set("email",$email)
            ->set("name",$name)
            ->set("gallery_id",$gallery_id)
            ->set("time","NOW()",false)
            ->set("image_id",$work_id);
        $this->db->insert("artist_page_visits");
    }

    public function get_earliest_month() {
        $this->db->select("date_format(time,'%Y-%m') as 'date'")
            ->from("artist_page_visits")
            ->order_by("time")
            ->limit(1);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $row = $query->row_array();
            return $row["date"];
        }
        return null;
    }

    public function get_visitors($year_month=null) {
        $this->db->select("email, name, date_format(time,'%e') as 'day', count(distinct artist_page_visits.image_id) as 'work_count', count(distinct artist_id) as 'artist_count'",false)
            ->from("artist_page_visits")
            ->join("image_artist","image_artist.image_id = artist_page_visits.image_id");
        if (!$year_month) {
            $year_month = date("Y-m");
        }
        $this->db->where("date_format(time,'%Y-%m') = ".$this->db->escape($year_month),null,false);
        $this->db->group_by("email");
        $this->db->order_by("time","desc");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_artists($year_month=null) {
        $this->db->select("artist.id, artist.name, count(distinct artist_page_visits.image_id) as 'work_count', count(distinct email) as 'visitor_count'")
            ->from("artist_page_visits")
            ->join("image_artist","image_artist.image_id = artist_page_visits.image_id")
            ->join("artist","artist.id = image_artist.artist_id");
        if (!$year_month) {
            $year_month = date("Y-m");
        }
        $this->db->where("date_format(time,'%Y-%m') = ".$this->db->escape($year_month),null,false);
        $this->db->group_by("artist.id");
        $this->db->order_by("artist.name");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_artist_counts($year_month=null) {
        $this->db->select("date_format(time,'%e') as 'day', count(distinct artist_page_visits.image_id) as 'work_count', count(distinct artist_id) as 'artist_count'",false)
            ->from("artist_page_visits")
            ->join("image_artist","image_artist.image_id = artist_page_visits.image_id");
        if (!$year_month) {
            $year_month = date("Y-m");
        }
        $this->db->where("date_format(time,'%Y-%m') = ".$this->db->escape($year_month),null,false);
        $this->db->group_by("day");
        $this->db->order_by("time","asc");
        $query = $this->db->get();
        $days = array();
        if ($query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $days[intval($row["day"])] = $row;
            }
        }
        return $days;
    }

    public function get_visitor_counts($year_month=null) {
        $this->db->select("date_format(time,'%e') as 'day', count(distinct artist_page_visits.image_id) as 'work_count', count(distinct email) as 'visitor_count'",false)
            ->from("artist_page_visits");
        if (!$year_month) {
            $year_month = date("Y-m");
        }
        $this->db->where("date_format(time,'%Y-%m') = ".$this->db->escape($year_month),null,false);
        $this->db->group_by("day");
        $this->db->order_by("time","asc");
        $query = $this->db->get();
        $days = array();
        if ($query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $days[intval($row["day"])] = $row;
            }
        }
        return $days;
    }

    public function get_artist_visitors($artist_id,$year_month=null) {
        $this->db->select("email, name, time, count(distinct artist_page_visits.image_id) as 'work_count'")
            ->from("artist_page_visits")
            ->join("image_artist","image_artist.image_id = artist_page_visits.image_id")
            ->where("artist_id",$artist_id);
        if (!$year_month) {
            $year_month = date("Y-m");
        }
        $this->db->where("date_format(time,'%Y-%m') = ".$this->db->escape($year_month),null,false);
        $this->db->group_by("email");
        $this->db->order_by("name");
        $this->db->order_by("time","desc");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_visitor_artists($email,$year_month=null) {
        $this->db->select("artist.id, artist.name, time, count(distinct artist_page_visits.image_id) as 'work_count'")
            ->from("artist_page_visits")
            ->join("image_artist","image_artist.image_id = artist_page_visits.image_id")
            ->join("artist","artist.id = image_artist.artist_id")
            ->where("email",$email);
        if (!$year_month) {
            $year_month = date("Y-m");
        }
        $this->db->where("date_format(time,'%Y-%m') = ".$this->db->escape($year_month),null,false);
        $this->db->group_by("artist.id");
        $this->db->order_by("artist.name");
        $this->db->order_by("time","desc");
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_visits($artist_id,$email,$year_month) {
        $this->db->distinct()->select("image.id, work_width, work_height, work_depth, work_creation_year, lang, title, description, version, time, count(distinct time) as 'view_count'")
            ->from("image")
            ->join("artist_page_visits","artist_page_visits.image_id = image.id")
            ->join("image_artist","image_artist.image_id = artist_page_visits.image_id")
            ->join("image_translation","image_translation.image_id = image.id","left")
            ->where("email",$email)
            ->where("date_format(time,'%Y-%m') = ".$this->db->escape($year_month),null,false)
            ->where("image_artist.artist_id",$artist_id)
            ->group_by("image.id")
            ->order_by("count(distinct time)","desc")
            ->order_by("time","desc");
        $query = $this->db->get();
        if ($query->num_rows()) {
            $images = array();
            foreach ($query->result_array() as $row) {
                if (empty($images[$row["id"]])) {
                    $images[$row["id"]] = array(
                        "id"=>$row["id"],
                        "version"=>$row["version"],
                        "view_count"=>$row["view_count"]
                    );
                    if ($row["work_width"]) {
                        $images[$row["id"]]["width"] = floatval($row["work_width"]);
                    }
                    if ($row["work_height"]) {
                        $images[$row["id"]]["height"] = floatval($row["work_height"]);
                    }
                    if ($row["work_depth"]) {
                        $images[$row["id"]]["depth"] = floatval($row["work_depth"]);
                    }
                    if ($row["work_creation_year"]) {
                        $images[$row["id"]]["year"] = $row["work_creation_year"];
                    }
                }
                if ($row["title"]) {
                    $images[$row["id"]]["title"][$row["lang"]] = $row["title"];
                }
                if ($row["description"]) {
                    $images[$row["id"]]["description"][$row["lang"]] = $row["description"];
                }
            }
            return array_values($images);
        }
        return array();
    }
}