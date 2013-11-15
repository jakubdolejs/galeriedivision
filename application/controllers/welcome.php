<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'dg_controller.php';

/**
 * Class Welcome
 * @property Artist_model $artist_model
 * @property Exhibition_model $exhibition_model
 * @property News_model $news_model
 */

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

    public function xml_sitemap() {
        $this->load->helper("url");

        $links = array(
            site_url(),
            site_url().'montreal/exhibitions',
            site_url().'toronto/exhibitions',
            site_url().'montreal/artists',
            site_url().'toronto/artists',
            site_url().'montreal/news',
            site_url().'toronto/news',
            site_url().'montreal/contact',
            site_url().'toronto/contact'
        );
        $localised_links = array();

        $this->load->model("exhibition_model");
        $this->load->model("artist_model");
        $this->load->model("news_model");

        $exhibitions = $this->exhibition_model->get_all_exhibition_ids();
        foreach ($exhibitions as $gallery=>$shows) {
            foreach ($shows as $show) {
                $links[] = site_url().$gallery.'/exhibition/'.$show;
            }
        }

        $languages = array("fr","en");
        $artists = $this->artist_model->get_all_artist_ids();
        foreach ($artists as $gallery=>$people) {
            foreach ($people as $artist) {
                $links[] = site_url().$gallery.'/artist/'.$artist;
                foreach ($languages as $lang) {
                    $filename = "download/cv/".$artist."-".$lang.".pdf";
                    if (file_exists(rtrim(FCPATH,"/")."/".$filename)) {
                        $localised_links[] = site_url().$filename;
                    }
                }
            }
        }
        $artist_exhibition = $this->artist_model->get_all_artist_exhibitions();
        foreach ($artist_exhibition as $gallery=>$artists) {
            foreach ($artists as $artist=>$exhibitions) {
                $links[] = site_url().$gallery.'/artist/'.$artist.'/exhibitions';
                foreach ($exhibitions as $exhibition) {
                    $links[] = site_url().$gallery.'/artist/'.$artist.'/exhibition/'.$exhibition;
                }
            }
        }
        $artist_news = $this->artist_model->get_all_artists_with_news();
        foreach ($artist_news as $gallery=>$artists) {
            foreach ($artists as $artist) {
                $links[] = site_url().$gallery.'/artist/'.$artist.'/news';
            }
        }
        $all_news = $this->news_model->get_all_news_ids();
        foreach ($all_news as $gallery=>$news) {
            foreach ($news as $story) {
                $links[] = site_url().$gallery.'/news/'.$story;
            }
        }

        header("Content-type: text/xml; charset=utf-8");
        echo '<?xml version="1.0" encoding="UTF-8"?>
';
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:xhtml="http://www.w3.org/1999/xhtml">';

        foreach ($links as $link) {
            echo '<url><loc>'.$link.'</loc><changefreq>daily</changefreq><xhtml:link rel="alternate" hreflang="fr" href="'.$link.'?language=fr" /><xhtml:link rel="alternate" hreflang="en" href="'.$link.'?language=en" /></url>';
        }
        foreach ($localised_links as $link) {
            echo '<url><loc>'.$link.'</loc><changefreq>daily</changefreq></url>';
        }
        echo '</urlset>';
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */