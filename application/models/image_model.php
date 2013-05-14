<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Image_model extends CI_Model {

    function __construct() {
        $this->load->database();
    }

    public function insert($width,$height) {
        $this->db->set("image_width",$width)
            ->set("image_height",$height);
        if ($this->db->insert("image")) {
            return $this->db->insert_id();
        }
        return null;
    }

    public function delete($image_id) {
        $this->db->where("id",$image_id);
        $this->db->delete("image");
    }

    public function update($image_id,$width,$height,$depth,$year,$artists,$title,$description) {
        $this->db->set("work_width",$width)
            ->set("work_height",$height)
            ->set("work_depth",$depth)
            ->set("work_creation_year",$year)
            ->where("id",$image_id);
        $this->db->update("image");

        $this->db->where("image_id",$image_id);
        $this->db->delete("image_artist");
        if (!empty($artists)) {
            $inserts = array();
            foreach ($artists as $artist) {
                $inserts[] = array(
                    "artist_id"=>$artist,
                    "image_id"=>$image_id
                );
            }
            $this->db->insert_batch("image_artist",$inserts);
        }

        foreach (array("en","fr") as $lang) {
            if (isset($title[$lang]) || isset($description[$lang])) {
                $this->db->where("image_id",$image_id);
                $this->db->where("lang",$lang);
                $action = "insert";
                if ($this->db->count_all_results("image_translation")) {
                    $action = "update";
                }
                if (isset($title[$lang])) {
                    $this->db->set("title",$title[$lang]);
                } else {
                    $this->db->set("title","");
                }
                if (isset($description[$lang])) {
                    $this->db->set("description",$description[$lang]);
                } else {
                    $this->db->set("description","");
                }
                if ($action == "update") {
                    $this->db->where("image_id",$image_id);
                    $this->db->where("lang",$lang);
                    $this->db->update("image_translation");
                } else {
                    $this->db->set("lang",$lang);
                    $this->db->set("image_id",$image_id);
                    $this->db->insert("image_translation");
                }
            }
        }
    }

    public function get_list() {
        $this->db->select("image.id, artist_id, artist.name")
            ->from("image")
            ->join("image_artist JOIN artist ON image_artist.artist_id = artist.id","image.id = image_artist.image_id","left")
            ->order_by("image.id","desc");
        $query = $this->db->get();
        if ($query->num_rows()) {
            $images = array();
            foreach ($query->result_array() as $row) {
                if (empty($images[$row["id"]])) {
                    $images[$row["id"]] = array(
                        "id"=>$row["id"],
                        "artists"=>array()
                    );
                }
                if ($row["artist_id"]) {
                    $images[$row["id"]]["artists"][$row["artist_id"]] = $row["name"];
                }
            }
            return array_values($images);
        }
        return array();
    }

    public function get($image_id) {
        $this->db->select("image.id, work_width, work_height, work_depth, work_creation_year, artist_id, artist.name as 'artist_name', exhibition_id, image_translation.title, image_translation.description, image_translation.lang, gallery_id")
            ->from("image")
            ->join("image_artist JOIN artist ON image_artist.artist_id = artist.id","image.id = image_artist.image_id","left")
            ->join("image_exhibition","image.id = image_exhibition.image_id","left")
            ->join("image_translation","image.id = image_translation.image_id","left")
            ->join("image_gallery","image.id = image_gallery.image_id","left")
            ->where("image.id",$image_id);
        $query = $this->db->get();
        if ($query->num_rows()) {
            $image = array();
            foreach ($query->result_array() as $row) {
                if (empty($image)) {
                    $image = array(
                        "id"=>$row["id"],
                        "width"=>$row["work_width"] ? floatval($row["work_width"]) : null,
                        "height"=>$row["work_height"] ? floatval($row["work_height"]) : null,
                        "depth"=>$row["work_depth"] ? floatval($row["work_depth"]) : null,
                        "creation_year"=>$row["work_creation_year"],
                        "artists"=>array(),
                        "exhibitions"=>array(),
                        "title"=>array(),
                        "description"=>array(),
                        "galleries"=>array()
                    );
                }
                if ($row["artist_id"]) {
                    $image["artists"][$row["artist_id"]] = $row["artist_name"];
                }
                if ($row["exhibition_id"]) {
                    $image["exhibitions"][] = $row["exhibition_id"];
                }
                if ($row["title"]) {
                    $image["title"][$row["lang"]] = $row["title"];
                }
                if ($row["description"]) {
                    $image["description"][$row["lang"]] = $row["description"];
                }
                if ($row["gallery_id"]) {
                    $image["galleries"][] = $row["gallery_id"];
                }
            }
            return $image;
        }
        return null;
    }
}