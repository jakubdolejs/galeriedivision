<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "admin.php";

/**
 * Class Api
 * @property Artist_model $artist_model
 * @property Gallery_model $gallery_model
 * @property Image_model $image_model
 * @property Exhibition_model $exhibition_model
 * @property Staff_model $staff_model
 */

class Api extends Admin {

    function __construct() {
        parent::__construct();
        $this->load->model("artist_model");
        $this->load->model("gallery_model");
        $this->load->model("image_model");
        $this->load->model("exhibition_model");
        $this->load->model("staff_model");
    }

    public function artists() {
        $artists = $this->artist_model->get_artists();
        $this->load->view("json",array("data"=>$artists));
    }

    public function galleries() {
        $galleries = $this->gallery_model->get_galleries();
        $this->load->view("json",array("data"=>$galleries));
    }

    public function artist_images($artist_id) {
        $images = $this->image_model->get_artist_images($artist_id);
        $this->load->view("json",array("data"=>$images));
    }

    public function images() {
        $images = $this->image_model->get_list();
        $this->load->view("json",array("data"=>$images));
    }

    public function artist_gallery_images($artist_id,$gallery_id) {
        if ($this->input->post("images") !== false) {
            $user = $this->get_logged_in_user();
            if (!$user) {
                $this->output_login_error();
                return;
            }
            if (!$user["superuser"] && !in_array($gallery_id,$user["galleries"])) {
                $this->output_error("Not permitted to edit content for gallery ".$gallery_id);
                return;
            }
            $images = $this->input->post("images",true);
            $updated = $this->image_model->set_artist_images($user["id"],$artist_id,$gallery_id,$images);
            $this->load->view("json",array("data"=>$updated));
        } else {
            $images = $this->image_model->get_artist_images_with_details($artist_id,$gallery_id);
            $this->load->view("json",array("data"=>$images));
        }
    }

    public function delete_cv($artist_id,$lang) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            $this->output_login_error();
            return;
        }
        $artist = $this->artist_model->get_artist($artist_id);
        if (!$artist) {
            $this->output_error("Artist ".$artist_id." does not exist in the database.");
            return;
        }
        if (!in_array($lang,array("en","fr"))) {
            $this->output_error("The language attribute must be set to either 'en' or 'fr'.");
            return;
        }
        $filename = rtrim(FCPATH,"/")."/cv_pdf/".$artist_id."-".$lang.".pdf";
        $success = true;
        if (file_exists($filename)) {
            $success = unlink($filename);
        }
        $this->load->view("json",array("data"=>intval($success)));
    }

    public function exhibition_images($exhibition_id) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $exhibition = $this->exhibition_model->get_exhibition($exhibition_id);
        reset($exhibition["spaces"]);
        $exhibition_gallery_id = current($exhibition["spaces"]);
        $exhibition_gallery_id = $exhibition_gallery_id["gallery_id"];
        if (!$user["superuser"] && !in_array($exhibition_gallery_id,$user["galleries"])) {
            $this->output_error("Not permitted to edit content for exhibition ".$exhibition_id);
            return;
        }
        if ($this->input->post("images") !== false) {
            $images = $this->input->post("images",true);
            $updated = $this->image_model->set_exhibition_images($user["id"],$exhibition_id,$images);
            $this->load->view("json",array("data"=>$updated));
        }
    }

    public function order_staff() {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $priority = $this->input->post("priority",true);
        if ($priority && !empty($priority)) {
            if ($this->staff_model->reorder($user["id"],$priority)) {
                $this->load->view("json",array("data"=>true));
                return;
            }
        }
        $this->output_error("Error setting order of staff.");
    }

    protected function output_login_error() {
        $this->load->view("json",array("data"=>array("error"=>"Error loggin in")));
    }

    protected function output_error($error) {
        $this->load->view("json",array("data"=>array("error"=>$error)));
    }
}