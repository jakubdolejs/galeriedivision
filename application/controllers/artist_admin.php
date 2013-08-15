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
            $id = $this->artist_model->add($user["id"],$name);
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
        if (!$artist) {
            $this->output->append_output('<h1>Error</h1><p>The artist '.$artist_id.' does not exist in the database.</p>');
            $this->load->view("admin/footer");
            return;
        }
        if ($this->input->post("save")) {
            $name = $this->input->post("name",true);
            $this->artist_model->update_name($user["id"],$artist_id,$name);
            $listed = array();
            $represented = array();
            if ($this->input->post("status")) {
                foreach ($this->input->post("status",true) as $id=>$status) {
                    switch ($status) {
                        case "listed":
                            $listed[$id] = 1;
                            $represented[$id] = 0;
                            break;
                        case "represented":
                            $listed[$id] = 1;
                            $represented[$id] = 1;
                            break;
                        default:
                            $listed[$id] = 0;
                            $represented[$id] = 0;
                    }

                }
            }
            $image_id = $this->input->post("image_id");
            foreach ($artist["galleries"] as $id=>$gallery) {
                if (!$user["superuser"] && !in_array($id,$user["galleries"])) {
                    continue;
                }
                $image = !empty($image_id[$id]) ? $image_id[$id] : null;
                $this->artist_model->update_gallery_info($user["id"],$artist_id,$id,!empty($listed[$id]),!empty($represented[$id]),$image);
            }
            foreach (array("en","fr") as $lang) {
                $error = $this->save_pdf($artist_id,$lang);
                if ($error) {
                    $this->output->append_output('<h1>Error saving CV</h1><p>'.$error.'</p>');
                    $this->load->view("admin/footer.php");
                    return;
                }
            }
            if ($this->input->post("cv")) {
                $cv = $this->input->post("cv",true);
                foreach ($cv as $lang=>$text) {
                    $this->artist_model->update_cv($user["id"],$artist_id,$lang,$text);
                }
            }
            $this->output->append_output('<h1>Success</h1><p>'.$name.'\'s record has been updated.</p><p><a class="button" href="/admin/artists">OK</a></p>');
        } else {
            $images = $this->image_model->get_artist_images($artist_id);
            $this->load->view("admin/artist",array("artist"=>$artist,"user"=>$user,"images"=>$images));
        }
        $this->load->view("admin/footer.php");
    }

    private function save_pdf($id,$lang) {
        $error = false;
        if (isset($_FILES["pdf"]) && !empty($_FILES["pdf"]["name"][$lang])) {
            switch ($_FILES["pdf"]["error"][$lang]) {
                case UPLOAD_ERR_INI_SIZE:
                    //Value: 1; The uploaded file exceeds the upload_max_filesize directive in php.ini.
                    $error = "The uploaded file exceeds the maximum permitted upload size.";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    //Value: 2; The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.
                    $error = "The uploaded file exceeds the maximum permitted upload size.";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    //Value: 3; The uploaded file was only partially uploaded.
                    $error = "The file was only partially uploaded.";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    //Value: 4; No file was uploaded.
                    $error = "No file was uploaded.";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    //Value: 6; Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.
                    $error = "Missing a temporary folder.";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    //Value: 7; Failed to write file to disk. Introduced in PHP 5.1.0.
                    $error = "Failed to write the uploaded file to disk.";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    //Value: 8; A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help. Introduced in PHP 5.2.0.
                    $error = "A PHP extension stopped the file upload.";
                    break;
                default:
                    if (strtolower(pathinfo($_FILES["pdf"]["name"][$lang],PATHINFO_EXTENSION)) != "pdf") {
                        $error = "The uploaded file must be a PDF.";
                    } else if (!move_uploaded_file($_FILES["pdf"]["tmp_name"][$lang],rtrim(FCPATH,"/")."/cv_pdf/".$id."-".$lang.".pdf")) {
                        $error = "Error moving uploaded PDF file.";
                    } else {
                        $this->load->driver("cache");
                        $this->cache->memcached->clean();
                    }
            }
        }
        return $error;
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
            if ($this->artist_model->delete($user['id'],$artist_id)) {
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