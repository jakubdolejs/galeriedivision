<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'dg_controller.php';

class Welcome extends Dg_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -  
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in 
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
	public function index()
	{
        $lang = $this->config->item("language");
        $this->load->model("exhibition_model");
        $header_vars = $this->get_header_vars(NULL);

        $exhibitions = array();
        foreach ($header_vars["galleries"] as $gallery) {
            $exhibitions[$gallery["id"]] = $this->exhibition_model->get_exhibitions("current", $lang, $gallery["id"]);
            if (empty($exhibitions[$gallery["id"]])) {
                $exhibitions[$gallery["id"]] = $this->exhibition_model->get_exhibitions("upcoming", $lang, $gallery["id"]);
            }
            if (empty($exhibitions[$gallery["id"]])) {
                $exhibitions[$gallery["id"]] = $this->exhibition_model->get_exhibitions("past", $lang, $gallery["id"]);
            }
        }
        $this->load->view('header',$header_vars);
        $header_vars["exhibitions"] = $exhibitions;
        $this->load->view('home',$header_vars);
        $this->load->view('footer');
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */