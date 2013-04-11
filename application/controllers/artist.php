<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'dg_controller.php';

/**
 *@property Artist_model $artist_model 
 */

class Artist extends Dg_controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model("artist_model");
    }
    
    public function index($gallery_id) {
        $header_vars = $this->get_header_vars($gallery_id);
        $this->load->view("header",$header_vars);
        $artists = $this->artist_model->get_artists($gallery_id);
        $this->load->view("artist",array("artists"=>$artists,"gallery_id"=>$gallery_id));
        $this->load->view("footer");
    }
    
    public function view($artist_id) {
        
    }
}