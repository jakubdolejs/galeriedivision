<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Class Api
 * @property Artist_model $artist_model
 * @property Gallery_model $gallery_model
 */

class Api extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("artist_model");
        $this->load->model("gallery_model");
    }

    public function artists() {
        $artists = $this->artist_model->get_artists();
        $this->load->view("json",array("data"=>$artists));
    }

    public function galleries() {
        $galleries = $this->gallery_model->get_galleries();
        $this->load->view("json",array("data"=>$galleries));
    }
}