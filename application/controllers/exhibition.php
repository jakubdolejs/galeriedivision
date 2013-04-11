<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'dg_controller.php';
/**
 * @property Exhibition_model $exhibition_model 
 */
class Exhibition extends Dg_controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model("exhibition_model");
    }
    
    public function index($gallery) {
        $lang = $this->config->item("language");
        $current = $this->exhibition_model->get_exhibitions("current", $lang, $gallery);
        $upcoming = $this->exhibition_model->get_exhibitions("upcoming", $lang, $gallery);
        $past = $this->exhibition_model->get_exhibitions("past", $lang, $gallery);
        $header_vars = $this->get_header_vars($gallery);
        $this->load->view("header",$header_vars);
        $this->load->view("exhibition",array("current"=>$current,"gallery_id"=>$gallery));
        $this->load->view("footer");
    }
    
    public function view($exhibition) {
        
    }
}