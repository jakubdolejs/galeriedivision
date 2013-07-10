<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'dg_controller.php';

/**
 * @property Artist_model $artist_model
 * @property Image_model $image_model
 */

class Image extends Dg_controller {

    function __construct() {
        parent::__construct();
        $this->load->model("artist_model");
        $this->load->model("image_model");
    }

    public function artist_image($gallery_id,$artist_id,$image_id) {
        $header_vars = $this->get_header_vars($gallery_id);
        $this->load->view("header",$header_vars);
        $artist = $this->artist_model->get_artist($artist_id);
        $image = $this->image_model->get($image_id);
        $lang = $this->config->item("language");
        $this->output->append_output('<h1>'.$artist["name"].'</h1>');
        $this->load->view("image",array("artist"=>$artist,"image"=>$image,"gallery_id"=>$gallery_id,"lang"=>$lang));
        $this->load->view("footer");
    }
}