<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

use Ctct\ConstantContact;
use Ctct\Components\Contacts\Contact;
use Ctct\Components\Contacts\ContactList;
use Ctct\Components\Contacts\EmailAddress;
use Ctct\Exceptions\CtctException;

require_once 'dg_controller.php';
/**
 *@property News_model $news_model
 *@property ConstantContact $cc
 */
class News extends Dg_controller {

    private $cc;
    private $cc_access_token;
    
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
            $this->output->append_output('<h1>'.$this->lang->line("News").'</h1>');
            if ($this->input->cookie("email")) {
                $this->loadConstantContact();
                $response = $this->cc->getContactByEmail($this->cc_access_token,$this->input->cookie("email"));
                if (empty($response)) {
                    $this->load->helper("cookie");
                    delete_cookie("email");
                    $this->load->view("subscribe");
                }
            } else {
                $this->load->view("subscribe");
            }
            $this->load->view("news",array("news"=>$news,"gallery_id"=>$gallery_id));
            $this->load->view("footer");
            $this->save_memcache($cache_key,$this->output->get_output());
        }
    }

    private function loadConstantContact() {
        require_once rtrim(FCPATH,"/").'/ctct/src/Ctct/autoload.php';
        $this->cc = new ConstantContact($this->config->item("ctct_api_key"));
        $this->cc_access_token = $this->config->item("ctct_access_token");
    }

    public function subscribe($gallery_id) {
        $header_vars = $this->get_header_vars($gallery_id);
        $this->load->view("header",$header_vars);
        if ($this->input->post("email")) {
            $this->loadConstantContact();
            try {
                $response = $this->cc->getContactByEmail($this->cc_access_token, strtolower($this->input->post("email",true)));
                if (empty($response->results)) {
                    $contact = new Contact();
                    $contact->addEmail(strtolower($this->input->post("email",true)));
                    $contact->first_name = $this->input->post("first_name",true);
                    $contact->last_name = $this->input->post("last_name",true);
                    $returnContact = $this->cc->addContact($this->cc_access_token, $contact, false);
                } else {
                    $contact = $response->results[0];
                    $contact->first_name = $this->input->post("first_name",true);
                    $contact->last_name = $this->input->post("last_name",true);
                    $returnContact = $this->cc->updateContact($this->cc_access_token, $contact, false);
                }
                $this->output->append_output('<h1>Thank you</h1><p>Thank you for subscribing to the Division Gallery newsletter.</p><p><a href="javascript:history.back()">Go back to previous page</a></p>');
                $this->output->append_output('<script type="text/javascript">
                saveContact("'.strtolower($this->input->post("email",true)).'",'.json_encode($contact->first_name.' '.$contact->last_name).');
                </script>');
            } catch (CtctException $error) {
                $this->output->append_output('<h1>Error</h1><p>'.$error->getMessage().'</p>');
            }
        } else {
            $this->output->append_output('<h1>Error</h1><p>Please enter your email address.</p>');
        }
        $this->load->view("footer");
    }
}