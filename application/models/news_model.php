<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class News_model extends CI_Model {
    
    function __construct() {
        parent::__construct();
        $this->load->database();
    }
    
    public function get_news($gallery_id,$lang,$max_stories=10) {
        $this->db->select("id, headline, media_outlet, text, date_published, url")
                ->from("news")
                ->join("news_gallery", "news_gallery.news_id = news.id")
                ->join("news_translation", "news_translation.news_id = news.id AND news_translation.lang = ".$this->db->escape($lang),"left")
                ->join("news_artist", "news_artist.news_id = news.id","left")
                ->join("news_exhibition", "news_exhibition.news_id = news.id","left")
                ->where("news_gallery.gallery_id",$gallery_id)
                ->order_by("date_published","DESC")
                ->limit($max_stories);
        $query = $this->db->get();
        return $query->result_array();
    }
}