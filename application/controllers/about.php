<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'dg_controller.php';

class About extends Dg_controller {
    
    public function index($gallery_id) {
        $header_vars = $this->get_header_vars($gallery_id);
        $this->load->view("header",$header_vars);
        $this->lang->load("common");
        $this->output->append_output('<h1>'.$this->lang->line("About").'</h1>');
        $this->load->view("footer");
    }
}