<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once "admin.php";

/**
 * Class Tracking
 * @property Tracking_model $tracking_model
 * @property Artist_model $artist_model
 */

class Tracking_admin extends Admin {

    function __construct() {
        parent::__construct();
        $this->load->model("tracking_model");
        $this->load->model("artist_model");
    }

    public function artists() {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $month = $this->input->get("month",true);
        if (!$month) {
            $month = date("Y-m");
        }
        $this->load->view("admin/header",array("user"=>$user));
        $this->load->view("admin/tracking_header",array("selected"=>"artists","month"=>$month));
        $days = $this->tracking_model->get_artist_counts($month);
        $artists = $this->tracking_model->get_artists($month);
        $this->load->view("admin/tracking_artists",array("month"=>$month,"days"=>$days,"artists"=>$artists));
        $this->load->view("admin/footer");
    }

    public function visitors() {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $month = $this->input->get("month",true);
        if (!$month) {
            $month = date("Y-m");
        }
        $this->load->view("admin/header",array("user"=>$user));
        $this->load->view("admin/tracking_header",array("selected"=>"visitors","month"=>$month));
        $days = $this->tracking_model->get_visitor_counts($month);
        $visitors = $this->tracking_model->get_visitors($month);
        $this->load->view("admin/tracking_visitors",array("visitors"=>$visitors,"days"=>$days,"month"=>$month));
        $this->load->view("admin/footer");
    }

    public function artist($id) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $month = $this->input->get("month",true);
        if (!$month) {
            $month = date("Y-m");
        }
        $artist = $this->artist_model->get_artist($id);
        $this->load->view("admin/header",array("user"=>$user));
        $this->load->view("admin/tracking_header",array("selected"=>"artists","month"=>$month));
        $visitors = $this->tracking_model->get_artist_visitors($id,$month);
        $this->load->view("admin/tracking_artist_visitors",array("visitors"=>$visitors,"artist"=>$artist,"month"=>$month));
        $this->load->view("admin/footer");
    }

    public function visitor() {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $email = $this->input->get("visitor",true);
        $name = $this->input->get("name",true);
        $month = $this->input->get("month",true);
        if (!$month) {
            $month = date("Y-m");
        }
        $this->load->view("admin/header",array("user"=>$user));
        $this->load->view("admin/tracking_header",array("selected"=>"visitors","month"=>$month));
        $artists = $this->tracking_model->get_visitor_artists($email,$month);
        $this->load->view("admin/tracking_visitor_artists",array("artists"=>$artists,"email"=>$email,"name"=>$name,"month"=>$month));
        $this->load->view("admin/footer");
    }

    public function visit($artist_id) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $email = $this->input->get("visitor",true);
        $name = $this->input->get("name",true);
        $month = $this->input->get("month",true);
        if (!$month) {
            $month = date("Y-m");
        }
        $artist = $this->artist_model->get_artist($artist_id);
        $this->load->view("admin/header",array("user"=>$user));
        $this->load->view("admin/tracking_header",array("month"=>$month));
        $visits = $this->tracking_model->get_visits($artist_id,$email,$month);
        $this->load->view("admin/tracking_visit",array("images"=>$visits,"artist"=>$artist,"name"=>$name));
        $this->load->view("admin/footer");
    }
}