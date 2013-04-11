<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *@property Gallery_model $gallery_model 
 */
class Dg_controller extends CI_Controller {
    
    protected function get_header_vars($gallery_id) {
        $this->load->model("gallery_model");
        $galleries = $this->gallery_model->get_galleries();
        return array("gallery_id"=>$gallery_id,"galleries"=>$galleries);
    }
}