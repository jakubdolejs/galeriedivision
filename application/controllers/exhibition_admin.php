<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "admin.php";

/**
 * Class Exhibition_admin
 * @property Exhibition_model $exhibition_model
 * @property Gallery_model $gallery_model
 * @property Artist_model $artist_model
 * @property Image_model $image_model
 */

class Exhibition_admin extends Admin {

    function __construct() {
        parent::__construct();
        $this->load->model("exhibition_model");
        $this->load->model("gallery_model");
        $this->load->model("artist_model");
        $this->load->model("image_model");
    }

    public function index($year=null) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header");

        if ($this->input->post("save")) {
            if (!$this->exhibition_model->update(null,$this->input->post(null,true),$error)) {
                $this->output->append_output("<h1>Error</h1><p>Error adding exhibition.</p>");
                if ($error) {
                    $this->output->append_output("<p>".$error."</p>");
                }
            } else {
                $this->output->append_output('<h1>Success</h1><p>Exhibition added.</p><p><a href="/admin/exhibitions">OK</a></p>');
            }
        } else {
            $this->output->append_output('<p><a href="/admin/exhibition/create">Add exhibition</a></p>');
            $exhibitions = $this->exhibition_model->get_all_exhibitions($year);
            $years = $this->exhibition_model->get_years($user["superuser"] ? null : $user["galleries"]);
            if ($year) {
                $this->output->append_output('<h1>'.$year.' Exhibitions</h1>');
            } else {
                $this->output->append_output('<h1>Recent Exhibitions</h1>');
            }
            $this->load->view("admin/exhibition_list",array("exhibitions"=>$exhibitions,"years"=>$years,"user"=>$user));
        }
        $this->load->view("admin/footer.php");
    }

    public function images($exhibition_id) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header");
        $exhibition = $this->exhibition_model->get_exhibition($exhibition_id);
        reset($exhibition["spaces"]);
        $exhibition_gallery_id = current($exhibition["spaces"]);
        $exhibition_gallery_id = $exhibition_gallery_id["gallery_id"];
        if (!$user["superuser"] && !in_array($exhibition_gallery_id,$user["galleries"])) {
            $this->output->append_output("<h1>Error</h1><p>You are not authorized to edit this exhibition.</p>");
        } else {
            $exhibition_images = $this->image_model->get_exhibition_images($exhibition_id);
            $all_images = $this->image_model->get_list();
            $titles = array_unique($exhibition["title"]);
            $this->load->view("admin/exhibition_images",array("exhibition_images"=>$exhibition_images,"all_images"=>$all_images,"exhibition_name"=>(empty($titles) == false ? join("/",$titles) : $exhibition["artist"]["name"]),"exhibition_id"=>$exhibition_id));
        }
        $this->load->view("admin/footer.php");
    }

    public function add() {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header");
        $spaces = $this->gallery_model->get_spaces($user["superuser"] ? null : $user["galleries"]);
        $artists = $this->artist_model->get_artists();
        $this->load->view("admin/exhibition",array("spaces"=>$spaces,"artists"=>$artists,"user"=>$user));
        $this->load->view("admin/footer");
    }

    public function edit($exhibition_id) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header");
        $exhibition = $this->exhibition_model->get_exhibition($exhibition_id);
        reset($exhibition["spaces"]);
        $exhibition_gallery_id = current($exhibition["spaces"]);
        $exhibition_gallery_id = $exhibition_gallery_id["gallery_id"];
        if (!$user["superuser"] && !in_array($exhibition_gallery_id,$user["galleries"])) {
            $this->output->append_output("<h1>Error</h1><p>You are not authorized to edit this exhibition.</p>");
        } else {
            if ($this->input->post("save")) {
                if (!$this->exhibition_model->update($exhibition_id,$this->input->post(null,true),$error)) {
                    $this->output->append_output("<h1>Error</h1><p>Error saving exhibition.</p>");
                    if ($error) {
                        $this->output->append_output("<p>".$error."</p>");
                    }
                } else {
                    $this->output->append_output('<h1>Success</h1><p>Exhibition saved.</p><p><a href="/admin/exhibitions">OK</a></p>');
                }
            } else {
                $spaces = $this->gallery_model->get_spaces($user["superuser"] ? null : $user["galleries"]);
                $artists = $this->artist_model->get_artists();
                $this->load->view("admin/exhibition",array("exhibition"=>$exhibition,"spaces"=>$spaces,"artists"=>$artists,"user"=>$user));
            }
        }
        $this->load->view("admin/footer");
    }

    public function delete($exhibition_id) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header");
        $exhibition = $this->exhibition_model->get_exhibition($exhibition_id);
        reset($exhibition["spaces"]);
        $exhibition_gallery_id = current($exhibition["spaces"]);
        $exhibition_gallery_id = $exhibition_gallery_id["gallery_id"];
        if (!$user["superuser"] && !in_array($exhibition_gallery_id,$user["galleries"])) {
            $this->output->append_output("<h1>Error</h1><p>You are not authorized to delete this exhibition.</p>");
        } else {
            if (!$this->exhibition_model->delete($exhibition_id)) {
                $this->output->append_output("<h1>Error</h1><p>Error deleting exhibition.</p>");
            } else {
                $this->output->append_output('<h1>Success</h1><p>Exhibition deleted.</p><p><a href="/admin/exhibitions">OK</a></p>');
            }
        }
    }
}