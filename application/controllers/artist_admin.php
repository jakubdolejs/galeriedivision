<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "admin.php";

/**
 * Class Image_admin
 * @property Image_model $image_model
 * @property Artist_model $artist_model
 * @property Gallery_model $gallery_model
 */

class Artist_admin extends Admin {

    function __construct() {
        parent::__construct();
        $this->load->model("image_model");
        $this->load->model("artist_model");
        $this->load->model("gallery_model");
    }

    public function index() {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header",array("user"=>$user));
        if ($this->input->post("name")) {
            $name = $this->input->post("name",true);
            $id = $this->artist_model->add($name);
            if (!$id) {
                $this->output->append_output('<script type="text/javascript">alert("Error adding '.$name.'");</script>');
            } else {
                $this->output->append_output('<script type="text/javascript">location.href="/admin/artist/'.$id.'";</script>');
            }
        }
        $artists = $this->artist_model->get_artists();
        $this->output->append_output('<h1>Artists</h1>');
        $this->load->view("admin/artist_add");
        $this->load->view("admin/artist_list",array("artists"=>$artists,"user"=>$user));
        $this->load->view("admin/footer.php");
    }

    public function edit($artist_id) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header",array("user"=>$user));
        $artist = $this->artist_model->get_artist($artist_id);
        if ($this->input->post("save")) {
            $name = $this->input->post("name",true);
            $this->artist_model->update_name($artist_id,$name);
            $listed = $this->input->post("available");
            $represented = $this->input->post("represented");
            $image_id = $this->input->post("image_id");
            foreach ($artist["galleries"] as $id=>$gallery) {
                if (!$user["superuser"] && !in_array($id,$user["galleries"])) {
                    continue;
                }
                $image = !empty($image_id[$id]) ? $image_id[$id] : null;
                $this->artist_model->update_gallery_info($artist_id,$id,!empty($listed[$id]),!empty($represented[$id]),$image);
            }
            $this->output->append_output('<h1>Success</h1><p>'.$name.'\'s record has been updated.</p><p><a class="button" href="/admin/artists">OK</a></p>');
        } else {
            $images = $this->image_model->get_artist_images($artist_id);
            $this->load->view("admin/artist",array("artist"=>$artist,"user"=>$user,"images"=>$images));
        }
        $this->load->view("admin/footer.php");
    }

    public function delete($artist_id) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $name = $this->artist_model->get_name($artist_id);
        $this->load->view("admin/header",array("user"=>$user));
        if (!$this->artist_model->is_deletable_by_user($artist_id,$user)) {
            $this->output->append_output('<h1>Error</h1><p>The artist '.$name.' cannot be deleted. There may be exhibitions, news or images associated with the artist. Please delete them first before attempting to delete the artist.</p>');
        } else {
            if ($this->artist_model->delete($artist_id)) {
                $this->output->append_output('<h1>Success</h1><p>Artist '.$name.' has been deleted.</p>');
            } else {
                $this->output->append_output('<h1>Error</h1><p>We were unable to delete the artist '.$name.' at this time.</p>');
            }
        }
        $this->output->append_output('<p><a class="button" href="/admin/artists">OK</a></p>');
        $this->load->view("admin/footer.php");
    }

    public function images($artist_id,$gallery_id=null) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header",array("user"=>$user));
        if ($gallery_id) {
            $gallery_images = $this->image_model->get_artist_images($artist_id,$gallery_id);
            $all_images = $this->image_model->get_artist_images($artist_id);
            $artist = $this->artist_model->get_artist($artist_id);
            $gallery = $this->gallery_model->get_gallery($gallery_id);
            $this->load->view("admin/artist_gallery_images",array("gallery_images"=>$gallery_images,"all_images"=>$all_images,"artist_name"=>$artist["name"],"gallery_name"=>$gallery["city"],"artist_id"=>$artist_id,"gallery_id"=>$gallery_id));
        }
        $this->load->view("admin/footer.php");
    }
}