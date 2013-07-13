<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'dg_controller.php';
/**
 * @property Gallery_model $gallery_model 
 */

class Gallery extends Dg_controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model("gallery_model");
    }
    
    public function index($gallery) {
        $lang = $this->config->item("language");
        $cache_key = MemcacheKeys::contact($gallery,$lang);
        if (!$this->output_memcache_if_available($cache_key)) {
            $gallery_info = $this->gallery_model->get_gallery($gallery);
            $gallery_staff = $this->gallery_model->get_gallery_staff($gallery,$lang);
            $gallery_hours = $this->gallery_model->get_gallery_hours($gallery);

            $hours = array();
            $last_times = NULL;
            foreach ($gallery_hours as $times) {
                if (!$last_times || $times["open_time"] != $last_times["open_time"] || $times["close_time"] != $last_times["close_time"]) {
                    $hours[] = array("start"=>$this->lang->line("weekday_".$times["day"]),"open"=>strtotime($times["open_time"]),"close"=>strtotime($times["close_time"]));
                } else {
                    $hours[count($hours)-1]["end"] = $this->lang->line("weekday_".$times["day"]);
                }
                $last_times = $times;
            }
            $has_minutes = FALSE;
            foreach ($hours as $k=>$h) {
                $open = new DateTime();
                $open->setTimestamp($h["open"]);
                $close = new DateTime();
                $close->setTimestamp($h["close"]);
                if (!$has_minutes && ($open->format("i") != "00" || $close->format("i") != "00")) {
                    $has_minutes = TRUE;
                    break;
                }
            }
            $time_format = "ga";
            if ($has_minutes) {
                if ($lang != "en") {
                    $time_format = "H:i";
                } else {
                    $time_format = "g:ia";
                }
            } else if ($lang != "en") {
                $time_format = "H";
            }
            $opening_hours = array();
            foreach ($hours as $k=>$h) {
                $key = $h["start"];
                if (!empty($h["end"])) {
                    $key .= "–".$h["end"];
                }
                $open = new DateTime();
                $open->setTimestamp($h["open"]);
                $close = new DateTime();
                $close->setTimestamp($h["close"]);
                $opening_hours[$key] = $open->format($time_format)."–".$close->format($time_format);
            }
            $gallery_info["name"] = $this->lang->line("Division Gallery")." ".$this->lang->line($gallery_info["city"]);
            $header_vars = $this->get_header_vars($gallery);
            $this->load->view("header",$header_vars);
            $this->load->view("contact",array("info"=>$gallery_info,"staff"=>$gallery_staff,"hours"=>$opening_hours));
            $this->load->view("footer");
            $this->save_memcache($cache_key,$this->output->get_output());
        }
    }
}