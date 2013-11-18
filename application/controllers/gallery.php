<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'dg_controller.php';

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Components\Contacts\EmailAddress;
use Ctct\Exceptions\CtctException;

/**
 * @property Gallery_model $gallery_model
 * @property ConstantContact $cc
 */

class Gallery extends Dg_controller {

    private $cc;
    private $cc_access_token;
    
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
            $hours_microdata = array();
            $microdata_days = array(
                'http://purl.org/goodrelations/v1#Sunday',
                'http://purl.org/goodrelations/v1#Monday',
                'http://purl.org/goodrelations/v1#Tuesday',
                'http://purl.org/goodrelations/v1#Wednesday',
                'http://purl.org/goodrelations/v1#Thursday',
                'http://purl.org/goodrelations/v1#Friday',
                'http://purl.org/goodrelations/v1#Saturday'
            );
            foreach ($gallery_hours as $times) {
                $key = '<meta itemprop="opens" content="'.date("H:i:s",strtotime($times["open_time"])).'" /><meta itemprop="closes" content="'.date("H:i:s",strtotime($times["close_time"])).'" />';
                if (!isset($hours_microdata[$key])) {
                    $hours_microdata[$key] = array();
                }
                $hours_microdata[$key][] = '<link itemprop="dayOfWeek" href="'.$microdata_days[$times["day"]].'" />';
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
            $header_vars["title"] = $this->lang->line("Contact");
            $this->load->view("header",$header_vars);

            $this->loadConstantContact();
            $lists = $this->cc->getLists($this->cc_access_token);
            $lists = array_filter($lists,function($item){
                return $item->status == "ACTIVE";
            });

            $this->load->view("contact",array("info"=>$gallery_info,"staff"=>$gallery_staff,"hours"=>$opening_hours,"hours_microdata"=>$hours_microdata,"lists"=>$lists));
            $this->load->view("footer");
            $this->save_memcache($cache_key,$this->output->get_output());
        }
    }

    private function loadConstantContact() {
        require_once rtrim(FCPATH,"/").'/ctct/src/Ctct/autoload.php';
        $this->cc = new ConstantContact($this->config->item("ctct_api_key"));
        $this->cc_access_token = $this->config->item("ctct_access_token");
    }
}