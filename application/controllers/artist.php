<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'dg_controller.php';

/**
 * @property Artist_model $artist_model
 * @property Image_model $image_model
 * @property Exhibition_model $exhibition_model
 */

class Artist extends Dg_controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model("artist_model");
        $this->load->model("image_model");
        $this->load->model("exhibition_model");
    }
    
    public function index($gallery_id) {
        $header_vars = $this->get_header_vars($gallery_id);
        $this->load->view("header",$header_vars);
        $artists = $this->artist_model->get_artists($gallery_id);
        $this->load->view("artist",array("artists"=>$artists,"gallery_id"=>$gallery_id));
        $this->load->view("footer");
    }
    
    public function view($gallery_id,$artist_id) {
        $header_vars = $this->get_header_vars($gallery_id);
        $this->load->view("header",$header_vars);
        $artist = $this->artist_model->get_artist($artist_id);
        $images = $this->image_model->get_artist_images_with_details($artist_id,$gallery_id);
        $lang = $this->config->item("language");
        $this->output->append_output('<h1>'.$artist["name"].'</h1>');
        $links = array("links"=>$this->get_tabs($artist_id,$gallery_id,"images"));
        $this->load->view("link_group",$links);
        $this->load->view("artist_images",array("artist"=>$artist,"images"=>$images,"gallery_id"=>$gallery_id,"lang"=>$lang));
        $this->load->view("footer");
    }

    public function cv($gallery_id,$artist_id) {
        $header_vars = $this->get_header_vars($gallery_id);
        $this->load->view("header",$header_vars);
        $artist = $this->artist_model->get_artist($artist_id);
        $lang = $this->config->item("language");
        $this->output->append_output('<h1>'.$artist["name"].'</h1>');
        $links = array("links"=>$this->get_tabs($artist_id,$gallery_id,"cv"));
        $this->load->view("link_group",$links);
        $this->load->view("artist_cv",array("artist"=>$artist,"lang"=>$lang));
        $this->load->view("footer");
    }

    public function exhibitions($gallery_id,$artist_id) {
        $header_vars = $this->get_header_vars($gallery_id);
        $this->load->view("header",$header_vars);
        $artist = $this->artist_model->get_artist($artist_id);
        $exhibitions = $this->exhibition_model->get_artist_exhibitions($artist_id,$gallery_id);
        $lang = $this->config->item("language");
        $this->output->append_output('<h1>'.$artist["name"].'</h1>');
        $links = array("links"=>$this->get_tabs($artist_id,$gallery_id,"exhibitions"));
        $this->load->view("link_group",$links);
        $this->load->view("artist_exhibitions",array("artist"=>$artist,"exhibitions"=>$exhibitions,"gallery_id"=>$gallery_id,"lang"=>$lang));
        $this->load->view("footer");
    }

    public function exhibition($gallery_id,$artist_id,$exhibition_id) {
        $header_vars = $this->get_header_vars($gallery_id);
        $this->load->view("header",$header_vars);
        $artist = $this->artist_model->get_artist($artist_id);
        $lang = $this->config->item("language");
        $this->output->append_output('<h1>'.$artist["name"].'</h1>');
        $links = array("links"=>$this->get_tabs($artist_id,$gallery_id,"exhibitions"));
        $this->load->view("link_group",$links);
        $exhibition = $this->exhibition_model->get_exhibition($exhibition_id);
        $images = $this->image_model->get_exhibition_images($exhibition_id);
        $this->load->view("exhibition",array("exhibition"=>$exhibition,"images"=>$images,"gallery_id"=>$gallery_id,"artist_id"=>$artist_id,"lang"=>$lang));
        $this->load->view("footer");
    }

    public function image($gallery_id,$artist_id,$image_id) {
        $images = $this->image_model->get_artist_images_with_details($artist_id,$gallery_id);
        $base_url = "/".$gallery_id."/artist/".$artist_id."/image/";
        $lang = $this->config->item("language");
        $this->load->view("image",array("parent_url"=>"/".$gallery_id."/artist/".$artist_id,"base_url"=>$base_url,"images"=>$images,"image_id"=>$image_id,"gallery_id"=>$gallery_id,"lang"=>$lang));
    }

    public function exhibition_image($gallery_id,$artist_id,$exhibition_id,$image_id) {
        $images = $this->image_model->get_exhibition_images_with_details($exhibition_id);
        $base_url = "/".$gallery_id."/artist/".$artist_id."/exhibition/".$exhibition_id."/image/";
        $lang = $this->config->item("language");
        $this->load->view("image",array("parent_url"=>"/".$gallery_id."/artist/".$artist_id."/exhibition/".$exhibition_id,"base_url"=>$base_url,"images"=>$images,"image_id"=>$image_id,"gallery_id"=>$gallery_id,"lang"=>$lang));
    }

    private function get_tabs($artist_id,$gallery_id,$selected=null) {
        $links = array();
        $this->lang->load("common");
        $sections = $this->artist_model->get_artist_sections($artist_id,$gallery_id);
        if ($sections["images"]) {
            $links[] = array(
                "url"=>"/".$gallery_id."/artist/".$artist_id,
                "label"=>$this->lang->line("Works")
            );
            if ($selected == "images") {
                $links[0]["selected"] = true;
            }
        }
        if ($sections["exhibitions"]) {
            $links[] = array(
                "url"=>"/".$gallery_id."/artist/".$artist_id."/exhibitions",
                "label"=>$this->lang->line("Exhibitions")
            );
            if ($selected == "exhibitions") {
                $links[count($links)-1]["selected"] = true;
            }
        }
        if ($sections["cv"]) {
            $links[] = array(
                "url"=>"/".$gallery_id."/artist/".$artist_id."/cv",
                "label"=>$this->lang->line("CV")
            );
            if ($selected == "cv") {
                $links[count($links)-1]["selected"] = true;
            }
        }
        if ($sections["news"]) {
            $links[] = array(
                "url"=>"/".$gallery_id."/artist/".$artist_id."/news",
                "label"=>$this->lang->line("News")
            );
            if ($selected == "news") {
                $links[count($links)-1]["selected"] = true;
            }
        }
        return $links;
    }
}