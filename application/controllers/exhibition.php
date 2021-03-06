<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'dg_controller.php';
/**
 * @property Exhibition_model $exhibition_model
 * @property Image_model $image_model
 */
class Exhibition extends Dg_controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model("exhibition_model");
        $this->load->model("image_model");
    }
    
    public function index($gallery) {
        $lang = $this->config->item("language");
        $current = $this->exhibition_model->get_exhibitions("current", $lang, $gallery);
        $upcoming = $this->exhibition_model->get_exhibitions("upcoming", $lang, $gallery);
        $past = $this->exhibition_model->get_exhibitions("past", $lang, $gallery);
        $header_vars = $this->get_header_vars($gallery);
        $header_vars["title"] = $this->lang->line("Exhibitions");
        $this->load->view("header",$header_vars);
        $this->load->view("exhibitions",array("current"=>$current,"upcoming"=>$upcoming,"past"=>$past,"gallery_id"=>$gallery));
        $this->load->view("footer");
    }
    
    public function view($gallery_id,$exhibition_id) {
        $lang = $this->config->item("language");
        $cache_key = MemcacheKeys::exhibition($gallery_id,$exhibition_id,$lang);
        if (!$this->output_memcache_if_available($cache_key)) {
            $exhibition = $this->exhibition_model->get_exhibition($exhibition_id);
            $images = $this->image_model->get_exhibition_images($exhibition_id);
            $header_vars = $this->get_header_vars($gallery_id);
            if (!empty($exhibition["title"][$lang])) {
                $header_vars["title"] = $exhibition["title"][$lang];
            } else if (!empty($exhibition["title"])) {
                $header_vars["title"] = join("/",$exhibition["title"]);
            }
            $this->load->view("header",$header_vars);
            $this->load->view("exhibition",array("exhibition"=>$exhibition,"images"=>$images,"gallery_id"=>$gallery_id,"lang"=>$lang));
            $this->load->view("footer");
            $this->save_memcache($cache_key,$this->output->get_output());
        }
    }

    public function past($gallery_id) {
        $header_vars = $this->get_header_vars($gallery_id);
        $lang = $this->config->item("language");
        $past = $this->exhibition_model->get_exhibitions("past", $lang, $gallery_id);
        $header_vars["title"] = $this->lang->line("Past Exhibitions");
        $this->load->view("header",$header_vars);
        $this->output->append_output('<h1>'.$this->lang->line("Past Exhibitions").'</h1>');
        if (!empty($past)) {
            foreach ($past as $exhibition) {
                $this->load->view("exhibition_listing",array("exhibition"=>$exhibition));
            }
        }
    }

    public function image($gallery_id,$exhibition_id,$image_id) {
        $images = $this->image_model->get_exhibition_images_with_details($exhibition_id);
        $base_url = "/".$gallery_id."/exhibition/".$exhibition_id."/image/";
        $lang = $this->config->item("language");
        $this->load->view("image",array("parent_url"=>"/".$gallery_id."/exhibition/".$exhibition_id,"base_url"=>$base_url,"images"=>$images,"image_id"=>$image_id,"gallery_id"=>$gallery_id,"lang"=>$lang));
    }
}