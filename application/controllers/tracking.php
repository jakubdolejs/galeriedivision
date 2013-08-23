<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'dg_controller.php';

/**
 * Class Tracking
 * @property Tracking_model $tracking_model
 */

class Tracking extends Dg_Controller {

	public function track() {
        if ($this->input->post("email") && $this->input->post("gallery_id") && $this->input->post("work_id")) {
            ini_set("ignore_user_abort",true);
            set_time_limit(0);
            $this->load->model("tracking_model");
            $this->tracking_model->track_visit(
                $this->input->post("email",true),
                $this->input->post("name",true),
                $this->input->post("gallery_id",true),
                $this->input->post("work_id",true)
            );
        }
	}
}