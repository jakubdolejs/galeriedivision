<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'dg_controller.php';
/**
 *@property News_model $news_model 
 */
class News extends Dg_controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model("news_model");
    }
    
    public function index($gallery_id) {
        $lang = $this->config->item("language");
        $news = $this->news_model->get_news($gallery_id,$lang);
        $header_vars = $this->get_header_vars($gallery_id);
        $this->load->view("header",$header_vars);
        $this->load->view("news",array("news"=>$news,"gallery_id"=>$gallery_id));
        $this->load->view("footer");
    }
}