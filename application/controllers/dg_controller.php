<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 *@property Gallery_model $gallery_model 
 */
class Dg_controller extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->driver("cache");
        $this->load->helper("memcache_helper");
    }
    
    protected function get_header_vars($gallery_id=NULL) {
        $this->load->model("gallery_model");
        $galleries = $this->gallery_model->get_galleries();
        return array("gallery_id"=>$gallery_id,"galleries"=>$galleries,"title"=>null);
    }

    protected final function get_memcache($key) {
        if ($cache = $this->cache->memcached->get($key)) {
            return $cache;
        }
        return false;
    }

    protected final function output_memcache_if_available($key) {
        if ($cache = $this->cache->memcached->get($key)) {
            $this->output->set_output($cache);
            return true;
        }
        return false;
    }

    protected final function save_memcache($key,$value) {
        $this->cache->memcached->save($key,$value);
    }
}