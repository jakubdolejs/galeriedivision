<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'dg_controller.php';

/**
 * @property Artist_model $artist_model
 * @property Image_model $image_model
 * @property Exhibition_model $exhibition_model
 * @property News_model $news_model
 */

class Artist extends Dg_controller {
    
    function __construct() {
        parent::__construct();
        $this->load->model("artist_model");
        $this->load->model("image_model");
        $this->load->model("exhibition_model");
        $this->load->model("news_model");
    }
    
    public function index($gallery_id) {
        $lang = $this->config->item("language");
        $cache_key = MemcacheKeys::artists($gallery_id,$lang);
        if (!$this->output_memcache_if_available($cache_key)) {
            $header_vars = $this->get_header_vars($gallery_id);
            $header_vars["title"] = "Artists";
            $this->load->view("header",$header_vars);
            $artists = $this->artist_model->get_artists($gallery_id);
            $this->load->view("artist",array("artists"=>$artists,"gallery_id"=>$gallery_id));
            $this->load->view("footer");
            $this->save_memcache($cache_key,$this->output->get_output());
        }
    }
    
    public function view($gallery_id,$artist_id) {
        $lang = $this->config->item("language");
        $cache_key = MemcacheKeys::artist($gallery_id,$artist_id,$lang);
        if (!$this->output_memcache_if_available($cache_key)) {
            $artist = $this->artist_model->get_artist($artist_id);
            $header_vars = $this->get_header_vars($gallery_id);
            $header_vars["title"] = $artist["name"];
            $this->load->view("header",$header_vars);
            $images = $this->image_model->get_artist_images_with_details($artist_id,$gallery_id);
            $this->output->append_output('<h1>'.$artist["name"].'</h1>');
            $links = array("links"=>$this->get_tabs($artist_id,$gallery_id,"images"));
            $this->load->view("link_group",$links);
            $this->load->view("artist_images",array("artist"=>$artist,"images"=>$images,"gallery_id"=>$gallery_id,"lang"=>$lang));
            $this->load->view("footer");
            $this->save_memcache($cache_key,$this->output->get_output());
        }
    }

    public function cv($gallery_id,$artist_id) {
        $lang = $this->config->item("language");
        $cache_key = MemcacheKeys::artist_cv($gallery_id,$artist_id,$lang);
        if (!$this->output_memcache_if_available($cache_key)) {
            $artist = $this->artist_model->get_artist($artist_id);
            $header_vars = $this->get_header_vars($gallery_id);
            $header_vars["title"] = $artist["name"]." – ".$this->lang->line("CV");
            $this->load->view("header",$header_vars);
            $this->output->append_output('<h1>'.$artist["name"].'</h1>');
            $links = array("links"=>$this->get_tabs($artist_id,$gallery_id,"cv"));
            $this->load->view("link_group",$links);
            $this->load->view("artist_cv",array("artist"=>$artist,"lang"=>$lang));
            $this->load->view("footer");
            $this->save_memcache($cache_key,$this->output->get_output());
        }
    }

    public function download_cv($file) {
        if (!preg_match('/^([a-z0-9_\-]+)\-(en|fr)\.pdf$/i',$file,$match)) {
            return;
        }
        $artist_id = $match[1];
        $lang = $match[2];
        $filename = rtrim(FCPATH,"/")."/cv_pdf/".$file;
        if (!file_exists($filename)) {
            $languages = array("en","fr");
            $found = false;
            foreach ($languages as $language) {
                $filename = rtrim(FCPATH,"/")."/cv_pdf/".$artist_id."-".$language.".pdf";
                if ($language != $lang && file_exists($filename)) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                return;
            }
        }
        $pdf = file_get_contents($filename);
        $this->output->set_header("Content-Type: application/pdf");
        $this->output->set_header("Content-Disposition: attachment");
        $this->output->set_header("Content-Length: ".strlen($pdf));
        $this->output->set_output($pdf);
    }

    public function exhibitions($gallery_id,$artist_id,$other_gallery=null) {
        $lang = $this->config->item("language");
        if ($other_gallery == null) {
            $cache_key = MemcacheKeys::artist_exhibitions($gallery_id,$artist_id,$lang);
        } else {
            $cache_key = MemcacheKeys::artist_exhibitions_other($gallery_id,$artist_id,$lang,$other_gallery);
        }
        if (!$this->output_memcache_if_available($cache_key)) {
            $artist = $this->artist_model->get_artist($artist_id);
            $header_vars = $this->get_header_vars($gallery_id);
            $header_vars["title"] = $artist["name"]." – ".$this->lang->line("Exhibitions");
            $this->load->view("header",$header_vars);
            $exhibition_gallery = $other_gallery == null ? $gallery_id : $other_gallery;
            $exhibitions = $this->exhibition_model->get_artist_exhibitions($artist_id,$exhibition_gallery);
            $this->output->append_output('<h1>'.$artist["name"].'</h1>');
            $selected_tab = $other_gallery == null ? "exhibitions" : "exhibitions_".$other_gallery;
            $links = array("links"=>$this->get_tabs($artist_id,$gallery_id,$selected_tab));
            $this->load->view("link_group",$links);
            $this->load->view("artist_exhibitions",array("artist"=>$artist,"exhibitions"=>$exhibitions,"gallery_id"=>$gallery_id,"lang"=>$lang));
            $this->load->view("footer");
            $this->save_memcache($cache_key,$this->output->get_output());
        }
    }

    public function exhibition($gallery_id,$artist_id,$exhibition_id) {
        $artist = $this->artist_model->get_artist($artist_id);
        $exhibition = $this->exhibition_model->get_exhibition($exhibition_id);
        $lang = $this->config->item("language");
        $header_vars = $this->get_header_vars($gallery_id);
        $header_vars["title"] = $artist["name"];
        if (!empty($exhibition["title"][$lang])) {
            $header_vars["title"] .= " – ".$exhibition["title"];
        } else if (!empty($exhibition["title"])) {
            $header_vars["title"] .= " – ".join("/",$exhibition["title"]);
        }
        $this->load->view("header",$header_vars);
        $this->output->append_output('<h1>'.$artist["name"].'</h1>');
        $links = array("links"=>$this->get_tabs($artist_id,$gallery_id,"exhibitions"));
        $this->load->view("link_group",$links);
        $images = $this->image_model->get_exhibition_images($exhibition_id);
        $this->load->view("exhibition",array("exhibition"=>$exhibition,"images"=>$images,"gallery_id"=>$gallery_id,"artist_id"=>$artist_id,"lang"=>$lang));
        $this->load->view("footer");
    }

    public function news($gallery_id,$artist_id) {
        $artist = $this->artist_model->get_artist($artist_id);
        $header_vars = $this->get_header_vars($gallery_id);
        $header_vars["title"] = $artist["name"]." – ".$this->lang->line("News");
        $this->load->view("header",$header_vars);
        $lang = $this->config->item("language");
        $this->output->append_output('<h1>'.$artist["name"].'</h1>');
        $links = array("links"=>$this->get_tabs($artist_id,$gallery_id,"news"));
        $this->load->view("link_group",$links);
        $news = $this->news_model->get_artist_news($gallery_id,$artist_id,$lang);
        $this->load->view("news",array("news"=>$news,"gallery_id"=>$gallery_id));
        $this->load->view("footer");
    }

    public function image($gallery_id,$artist_id,$image_id) {
        $images = $this->image_model->get_artist_images_with_details($artist_id,$gallery_id);
        $base_url = "/".$gallery_id."/artist/".$artist_id."/image/";
        $lang = $this->config->item("language");
        $this->load->view("image",array("parent_url"=>"/".$gallery_id."/artist/".$artist_id,"base_url"=>$base_url,"images"=>$images,"image_id"=>$image_id,"gallery_id"=>$gallery_id,"lang"=>$lang));
    }

    public function exhibition_image($gallery_id,$artist_id,$exhibition_id,$image_id) {
        $images = $this->image_model->get_exhibition_images_with_details($exhibition_id);
        $base_url = "/".$gallery_id."/artist/".$artist_id."/exhibition/".$exhibition_id."/image/";
        $lang = $this->config->item("language");
        $this->load->view("image",array("parent_url"=>"/".$gallery_id."/artist/".$artist_id."/exhibition/".$exhibition_id,"base_url"=>$base_url,"images"=>$images,"image_id"=>$image_id,"gallery_id"=>$gallery_id,"lang"=>$lang));
    }

    private function get_tabs($artist_id,$gallery_id,$selected=null) {
        $links = array();
        $sections = $this->artist_model->get_artist_sections($artist_id,$gallery_id);
        if ($sections["images"]) {
            $links[] = array(
                "url"=>"/".$gallery_id."/artist/".$artist_id,
                "label"=>$this->lang->line("Works")
            );
            if ($selected == "images") {
                $links[0]["selected"] = true;
            }
        }
        if (count($sections["exhibitions"]) > 0) {
            foreach ($sections["exhibitions"] as $gid => $city) {
                if ($gid == $gallery_id) {
                    $links[] = array(
                        "url"=>"/".$gallery_id."/artist/".$artist_id."/exhibitions",
                        "label"=>$this->lang->line("Exhibitions")
                    );
                    if ($selected == "exhibitions") {
                        $links[count($links)-1]["selected"] = true;
                    }
                } else {
                    $links[] = array(
                        "url"=>"/".$gallery_id."/artist/".$artist_id."/exhibitions_".$gid,
                        "label"=>$this->lang->line("Exhibitions")." (".$city.")"
                    );
                    if ($selected == "exhibitions_".$gid) {
                        $links[count($links)-1]["selected"] = true;
                    }
                }
            }
        }
        if ($sections["cv"]) {
            $links[] = array(
                "url"=>"/".$gallery_id."/artist/".$artist_id."/cv",
                "label"=>$this->lang->line("CV")
            );
            if ($selected == "cv") {
                $links[count($links)-1]["selected"] = true;
            }
        }
        if ($sections["news"]) {
            $links[] = array(
                "url"=>"/".$gallery_id."/artist/".$artist_id."/news",
                "label"=>$this->lang->line("News")
            );
            if ($selected == "news") {
                $links[count($links)-1]["selected"] = true;
            }
        }
        return $links;
    }
}