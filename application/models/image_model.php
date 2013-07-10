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

    public function get_artist_images($artist_id,$gallery_id=null) {
        $this->db->select("image.id, artist_id, artist.name")
            ->from("image")
            ->join("image_artist JOIN artist ON image_artist.artist_id = artist.id","image.id = image_artist.image_id","left");
        if ($gallery_id) {
            $this->db->join("image_gallery","image_gallery.image_id = image.id")
                ->where("gallery_id",$gallery_id)
                ->order_by("image_gallery.priority");
        }
        $this->db->where("artist_id",$artist_id)
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

    public function get_artist_images_with_details($artist_id,$gallery_id) {
        $this->db->distinct()->select("image.id, artist_id, artist.name, work_width, work_height, work_depth, work_creation_year, lang, title, description")
            ->from("image")
            ->join("image_artist JOIN artist ON image_artist.artist_id = artist.id","image.id = image_artist.image_id","left")
            ->join("image_gallery","image_gallery.image_id = image.id")
            ->join("image_translation","image_translation.image_id = image.id","left")
            ->where("gallery_id",$gallery_id)
            ->where("exists (select 1 from image_artist where image_artist.image_id = image.id and image_artist.artist_id = ".$this->db->escape($artist_id)." group by image.id)",null,false)
            ->order_by("image_gallery.priority")
            ->order_by("artist.name")
            ->order_by("image.id","desc");
        $query = $this->db->get();
        if ($query->num_rows()) {
            $images = array();
            foreach ($query->result_array() as $row) {
                if (empty($images[$row["id"]])) {
                    $images[$row["id"]] = array(
                        "id"=>$row["id"],
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
                if ($row["artist_id"]) {
                    $images[$row["id"]]["artists"][$row["artist_id"]] = $row["name"];
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

    public function set_artist_images($artist_id,$gallery_id,$images) {
        $this->db->trans_start();
        $query = "DELETE image_gallery FROM image_gallery JOIN image_artist ON image_artist.image_id = image_gallery.image_id WHERE image_artist.artist_id = ".$this->db->escape($artist_id)." AND image_gallery.gallery_id = ".$this->db->escape($gallery_id);
        $this->db->query($query,false,false);
        if (!empty($images)) {
            $batch = array();
            $i = 1;
            foreach ($images as $id) {
                $batch[] = array("image_id"=>$id,"gallery_id"=>$gallery_id,"priority"=>$i);
                $i ++;
            }
            $this->db->insert_batch("image_gallery",$batch);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }

    public function get_exhibition_images($exhibition_id) {
        $this->db->select("image.id, artist_id, artist.name")
            ->from("image")
            ->join("image_artist JOIN artist ON image_artist.artist_id = artist.id","image.id = image_artist.image_id","left")
            ->join("image_exhibition","image_exhibition.image_id = image.id")
            ->where("image_exhibition.exhibition_id",$exhibition_id)
            ->order_by("image_exhibition.priority")
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



    public function get_exhibition_images_with_details($exhibition_id) {
        $this->db->distinct()->select("image.id, artist_id, artist.name, work_width, work_height, work_depth, work_creation_year, lang, title, description")
            ->from("image")
            ->join("image_artist JOIN artist ON image_artist.artist_id = artist.id","image.id = image_artist.image_id","left")
            ->join("image_exhibition","image_exhibition.image_id = image.id")
            ->where("image_exhibition.exhibition_id",$exhibition_id)
            ->join("image_translation","image_translation.image_id = image.id","left")
            ->order_by("image_exhibition.priority")
            ->order_by("artist.name")
            ->order_by("image.id","desc");
        $query = $this->db->get();
        if ($query->num_rows()) {
            $images = array();
            foreach ($query->result_array() as $row) {
                if (empty($images[$row["id"]])) {
                    $images[$row["id"]] = array(
                        "id"=>$row["id"],
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
                if ($row["artist_id"]) {
                    $images[$row["id"]]["artists"][$row["artist_id"]] = $row["name"];
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

    public function set_exhibition_images($exhibition_id,$images) {
        $this->db->trans_start();
        $this->db->where("exhibition_id",$exhibition_id);
        $this->db->delete("image_exhibition");
        if (!empty($images)) {
            $batch = array();
            $i = 1;
            foreach ($images as $id) {
                $batch[] = array("image_id"=>$id,"exhibition_id"=>$exhibition_id,"priority"=>$i);
                $i ++;
            }
            $this->db->insert_batch("image_exhibition",$batch);
        }
        $this->db->trans_complete();
        return $this->db->trans_status() !== FALSE;
    }
}