<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Components\Contacts\EmailAddress;
use Ctct\Exceptions\CtctException;

require_once 'dg_controller.php';
/**
 *@property News_model $news_model 
 */
class News extends Dg_controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model("news_model");
    }
    
    public function index($gallery_id) {
        $lang = $this->config->item("language");
        $cache_key = MemcacheKeys::news($gallery_id,$lang);
        if (!$this->output_memcache_if_available($cache_key)) {
            $news = $this->news_model->get_news($gallery_id,$lang);
            $header_vars = $this->get_header_vars($gallery_id);

            $this->load->view("header",$header_vars);
            $this->load->view("news",array("news"=>$news,"gallery_id"=>$gallery_id));
            $this->load->view("footer");
            $this->save_memcache($cache_key,$this->output->get_output());
        }
    }

    public function subscribe() {
        if ($this->input->post("email")) {
            require_once rtrim(FCPATH,"/").'/ctct/src/Ctct/autoload.php';
            $cc = new ConstantContact($this->config->item("ctct_api_key"));

            $access_token = $this->config->item("ctct_access_token");

            try {
                $response = $cc->getContactByEmail($access_token, strtolower($this->input->post("email",true)));

                if (empty($response->results)) {
                    $contact = new Contact();
                    $contact->addEmail(strtolower($this->input->post("email",true)));
                    $contact->first_name = $this->input->post("first_name",true);
                    $contact->last_name = $this->input->post("last_name",true);
                    $returnContact = $cc->addContact($access_token, $contact, false);
                } else {
                    $contact = $response->results[0];
                    $contact->first_name = $this->input->post("first_name",true);
                    $contact->last_name = $this->input->post("last_name",true);
                    $returnContact = $cc->updateContact($access_token, $contact, false);
                }
            } catch (CtctException $error) {

            }
        }
    }
}