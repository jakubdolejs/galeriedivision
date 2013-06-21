<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "admin.php";

/**
 * Class Staff_admin
 * @property Staff_model $staff_model
 * @property Gallery_model $gallery_model
 */

class Staff_admin extends Admin {

    function __construct() {
        parent::__construct();
        $this->load->model("staff_model");
        $this->load->model("gallery_model");
    }

    public function index() {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        if ($this->input->post("save")) {
            if (!$user["superuser"] && !in_array($this->input->post("gallery_id",true),$user["galleries"])) {
                $this->output->append_output('<h1>Denied</h1><p>You are not permitted to edit the staff in '.$this->input->post("gallery_id",true).'</p>');
            } else {
                $this->staff_model->add($this->input->post("name",true),$this->input->post("email",true),$this->input->post("gallery_id",true),$this->input->post("title",true));
                $this->output->append_output('<h1>Success</h1><p>'.$this->input->post("name",true).' added.</p><p><a class="button" href="/admin/staff">OK</a></p>');
            }
        } else {
            $staff = $this->staff_model->get_staff($user["superuser"] ? null : $user["galleries"]);
            $this->load->view("admin/header",array("user"=>$user));
            $this->output->append_output('<h1>Gallery Staff</h1><p><a class="button" href="/admin/staff/create">Add staff</a></p>');
            $this->load->view("admin/staff_list",array("staff"=>$staff));
        }
        $this->load->view("admin/footer");
    }

    public function add() {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header",array("user"=>$user));
        $galleries = $this->gallery_model->get_galleries();
        $this->load->view("admin/staff",array("user"=>$user,"galleries"=>$galleries));
        $this->load->view("admin/footer");
    }

    public function edit($staff_id) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header",array("user"=>$user));
        if ($this->input->post("save")) {
            if (!$user["superuser"] && !in_array($this->input->post("gallery_id",true),$user["galleries"])) {
                $this->output->append_output('<h1>Denied</h1><p>You are not permitted to edit the staff in '.$this->input->post("gallery_id",true).'</p><p><a class="button" href="/admin/staff">OK</a></p>');
            } else {
                $this->staff_model->edit($staff_id,$this->input->post("name",true),$this->input->post("email",true),$this->input->post("gallery_id",true),$this->input->post("title",true));
                $this->output->append_output('<h1>Success</h1><p>'.$this->input->post("name",true).' updated.</p><p><a class="button" href="/admin/staff">OK</a></p>');
            }
        } else {
            $galleries = $this->gallery_model->get_galleries();
            $staff = $this->staff_model->get_staff_by_id($staff_id);
            $this->load->view("admin/staff",array("user"=>$user,"galleries"=>$galleries,"staff"=>$staff));
        }
        $this->load->view("admin/footer");
    }

    public function delete($staff_id) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header",array("user"=>$user));
        $staff = $this->staff_model->get_staff_by_id($staff_id);
        if (!$user["superuser"] && !in_array($staff["gallery"]["id"],$user["galleries"])) {
            $this->output->append_output('<h1>Denied</h1><p>You are not permitted to delete staff in '.$this->input->post("gallery_id",true).'</p><p><a class="button" href="/admin/staff">OK</a></p>');
        } else {
            $this->staff_model->delete($staff_id);
            $this->output->append_output('<h1>Success</h1><p>'.$staff["name"].' deleted.</p><p><a class="button" href="/admin/staff">OK</a></p>');
        }
        $this->load->view("admin/footer");
    }
}