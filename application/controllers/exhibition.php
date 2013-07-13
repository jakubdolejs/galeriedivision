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
        $this->load->view("header",$header_vars);
        $this->output->append_output('<h1>'.$this->lang->line("Past Exhibitions").'</h1>');
        if (!empty($past)) {
            foreach ($past as $exhibition) {
                $this->load->view("exhibition_listing",array("exhibition"=>$exhibition));
            }
        }
    }
}