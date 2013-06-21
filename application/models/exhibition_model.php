<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Exhibition_model extends CI_Model {
    
    function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function get_exhibitions($type,$lang,$gallery_id=NULL) {
        $this->db->select("exhibition.id, exhibition_translation.title, exhibition_translation.text, exhibition.start_date, exhibition.end_date, exhibition.reception_start, exhibition.reception_end, exhibition.image_id, space.name, space.gallery_id, artist.id as 'artist_id', artist.name as 'artist_name'")
                ->from("exhibition")
                ->join("exhibition_translation","exhibition_translation.exhibition_id = exhibition.id AND exhibition_translation.lang = ".$this->db->escape($lang),"left")
                ->join("space_exhibition","space_exhibition.exhibition_id = exhibition.id")
                ->join("space","space.id = space_exhibition.space_id")
                ->join("artist_exhibition JOIN artist ON artist_exhibition.artist_id = artist.id","artist_exhibition.exhibition_id = exhibition.id","left");
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

    public function get_all_exhibitions($year=null) {
        $this->db->select("exhibition.id, exhibition_translation.title, exhibition_translation.lang, exhibition_translation.text, exhibition.start_date, exhibition.end_date, exhibition.reception_start, exhibition.reception_end, exhibition.image_id, space.name, space.gallery_id, artist.id as 'artist_id', artist.name as 'artist_name'")
            ->from("exhibition")
            ->join("exhibition_translation","exhibition_translation.exhibition_id = exhibition.id","left")
            ->join("space_exhibition","space_exhibition.exhibition_id = exhibition.id")
            ->join("space","space.id = space_exhibition.space_id")
            ->join("artist_exhibition JOIN artist ON artist_exhibition.artist_id = artist.id","artist_exhibition.exhibition_id = exhibition.id","left")
            ->order_by("exhibition.start_date","DESC")
            ->order_by("lang");
        if (!$year) {
            $this->db->where("exhibition.start_date > now() - interval 1 year",null,false);
        } else {
            $this->db->where("date_format(exhibition.start_date,'%Y') = ".$this->db->escape($year),null,false)
                ->or_where("date_format(exhibition.end_date,'%Y') = ".$this->db->escape($year));
        }
        $this->db->order_by("priority");
        $this->db->order_by("artist.name");
        $query = $this->db->get();
        $exhibitions = array();
        if ($query->num_rows()) {
            foreach ($query->result_array() as $exhibition) {
                if (!array_key_exists($exhibition["id"], $exhibitions)) {
                    $exhibitions[$exhibition["id"]] = $exhibition;
                    if (isset($exhibitions[$exhibition["id"]]["artist_name"])) {
                        unset($exhibitions[$exhibition["id"]]["artist_name"]);
                    }
                    if (isset($exhibitions[$exhibition["id"]]["title"])) {
                        unset($exhibitions[$exhibition["id"]]["title"]);
                    }
                    unset($exhibitions[$exhibition["id"]]["lang"]);
                    $exhibitions[$exhibition["id"]]["artists"] = array();
                    $exhibitions[$exhibition["id"]]["titles"] = array();
                }
                if (!empty($exhibition["title"])) {
                    $exhibitions[$exhibition["id"]]["titles"][$exhibition["lang"]] = $exhibition["title"];
                }
                if (!empty($exhibition["artist_id"])) {
                    $exhibitions[$exhibition["id"]]["artists"][$exhibition["artist_id"]] = $exhibition["artist_name"];
                }
            }
        }
        foreach ($exhibitions as $id=>$exhibition) {
            $exhibitions[$id]["title"] = count(array_unique($exhibition["titles"])) == 1 ? current($exhibition["titles"]) : join("/",$exhibition["titles"]);
            if (strlen($exhibitions[$id]["title"]) == 0) {
                $exhibitions[$id]["title"] = join(", ",$exhibition["artists"]);
            } else if (count($exhibition["artists"]) < 3) {
                $exhibitions[$id]["title"] = join(" and ",$exhibition["artists"]).": ".$exhibitions[$id]["title"];
            }
        }
        return $exhibitions;
    }

    public function get_exhibition($exhibition_id) {
        $this->db->select("exhibition.id, exhibition_translation.title, exhibition_translation.lang, exhibition_translation.text, exhibition.start_date, exhibition.end_date, exhibition.reception_start, exhibition.reception_end, exhibition.image_id, space.id as 'space_id', space.name, space.gallery_id, artist.id as 'artist_id', artist.name as 'artist_name'")
            ->from("exhibition")
            ->join("exhibition_translation","exhibition_translation.exhibition_id = exhibition.id","left")
            ->join("space_exhibition","space_exhibition.exhibition_id = exhibition.id")
            ->join("space","space.id = space_exhibition.space_id")
            ->join("artist_exhibition JOIN artist ON artist_exhibition.artist_id = artist.id","artist_exhibition.exhibition_id = exhibition.id","left")
            ->where("exhibition.id",$exhibition_id)
            ->order_by("lang")
            ->order_by("artist.name");
        $query = $this->db->get();
        $exhibition = null;
        if ($query->num_rows()) {
            foreach ($query->result_array() as $row) {
                if (!$exhibition) {
                    $exhibition = array(
                        "id"=>$row["id"],
                        "title"=>array(),
                        "artists"=>array(),
                        "text"=>array(),
                        "start_date"=>$row["start_date"],
                        "end_date"=>$row["end_date"],
                        "reception_start"=>$row["reception_start"],
                        "reception_end"=>$row["reception_end"],
                        "image_id"=>$row["image_id"],
                        "spaces"=>array()
                    );
                }
                $exhibition["title"][$row["lang"]] = $row["title"];
                $exhibition["text"][$row["lang"]] = $row["text"];
                $exhibition["artists"][$row["artist_id"]] = $row["artist_name"];
                $exhibition["spaces"][$row["space_id"]] = array(
                    "id"=>$row["space_id"],
                    "name"=>$row["name"],
                    "gallery_id"=>$row["gallery_id"]
                );
            }
        }
        return $exhibition;
    }

    private function exhibition_id_exists($id) {
        $this->db->from("exhibition")
            ->where("id",$id);
        return $this->db->count_all_results() > 0;
    }

    public function update($exhibition_id,$values,&$errorinfo=null) {
        $this->db->trans_start();
        if (isset($values["start_date"])) {
            $this->db->set("start_date",$values["start_date"]);
        }
        if (isset($values["end_date"])) {
            $this->db->set("end_date",$values["end_date"]);
        }
        if (isset($values["reception_start"]) && isset($values["reception_starttime"]) && isset($values["reception_endtime"])) {
            if (empty($values["reception_start"]) || empty($values["reception_starttime"]) || empty($values["reception_endtime"])) {
                $this->db->set("reception_start",null);
                $this->db->set("reception_end",null);
            } else {
                $this->db->set("reception_start",$values["reception_start"]." ".$values["reception_starttime"].":00");
                $this->db->set("reception_end",$values["reception_start"]." ".$values["reception_endtime"].":00");
            }
        }
        if (isset($values["image_id"])) {
            if (empty($values["image_id"])) {
                $this->db->set("image_id",null);
            } else {
                $this->db->set("image_id",$values["image_id"]);
            }
        }
        if ($exhibition_id) {
            $this->db->where("id",$exhibition_id);
            $this->db->update("exhibition");
        } else {
            $base_id = "";
            $this->load->helper("text");
            if (!empty($values["title"])) {
                $titles = array();
                foreach ($values["title"] as $title) {
                    $titles[] = preg_replace("/[^a-z0-9]+/","-",convert_accented_characters(strtolower($title)));
                }
                $base_id = join("-",array_unique($titles));
            } else if (!empty($values["artist_ids"])) {
                $base_id = join("-",$values["artist_ids"]);
            } else {
                return false;
            }
            $exhibition_id = substr($base_id,0,128);
            $i = 1;
            while ($this->exhibition_id_exists($exhibition_id)) {
                $exhibition_id = substr($base_id,0,128-strlen("-".$i))."-".$i;
                $i ++;
            }
            $this->db->set("id",$exhibition_id);
            $this->db->insert("exhibition");
        }
        $this->db->where("exhibition_id",$exhibition_id);
        $this->db->delete("exhibition_translation");
        foreach (array("fr","en") as $lang) {
            if (!empty($values["title"][$lang])) {
                $this->db->set("title",$values["title"][$lang]);
            } else {
                $this->db->set("title",null);
            }
            if (!empty($values["text"][$lang])) {
                $this->db->set("text",$values["text"][$lang]);
            } else {
                $this->db->set("text",null);
            }
            $this->db->set("lang",$lang);
            $this->db->set("exhibition_id",$exhibition_id);
            $this->db->insert("exhibition_translation");
        }
        $this->db->where("exhibition_id",$exhibition_id);
        $this->db->delete("artist_exhibition");
        if (!empty($values["artist_ids"])) {
            $batch = array();
            foreach ($values["artist_ids"] as $artist_id) {
                $batch[] = array("artist_id"=>$artist_id,"exhibition_id"=>$exhibition_id);
            }
            $this->db->insert_batch("artist_exhibition",$batch);
        }
        $this->db->where("exhibition_id",$exhibition_id);
        $this->db->delete("space_exhibition");
        if (!empty($values["space_ids"])) {
            $batch = array();
            foreach ($values["space_ids"] as $space_id) {
                $batch[] = array("space_id"=>$space_id,"exhibition_id"=>$exhibition_id);
            }
            $this->db->insert_batch("space_exhibition",$batch);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== false;
    }

    public function delete($exhibition_id) {
        $this->db->trans_start();
        $this->db->where("exhibition_id",$exhibition_id);
        $this->db->delete("exhibition_translation");
        $this->db->where("exhibition_id",$exhibition_id);
        $this->db->delete("artist_exhibition");
        $this->db->where("exhibition_id",$exhibition_id);
        $this->db->delete("space_exhibition");
        $this->db->where("exhibition_id",$exhibition_id);
        $this->db->delete("image_exhibition");
        $this->db->where("id",$exhibition_id);
        $this->db->delete("exhibition");
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    public function get_years($gallery_ids=null) {
        $this->db->select("date_format(start_date,'%Y') as 'start', date_format(end_date,'%Y') as 'end'",false)
            ->distinct()
            ->from("exhibition");
        if (!empty($gallery_ids)) {
            $this->db->join("space_exhibition","space_exhibition.exhibition_id = exhibition.id")
                ->join("space","space.id = space_exhibition.space_id")
                ->where_in("space.gallery_id",$gallery_ids);
        }
        $this->db->order_by("start_date","desc");
        $query = $this->db->get();
        $years = array();
        if ($query->num_rows()) {
            foreach ($query->result_array() as $row) {
                $years[] = $row["start"];
                $years[] = $row["end"];
            }
            $years = array_unique($years,SORT_DESC|SORT_NUMERIC);
        }
        return $years;
    }
}