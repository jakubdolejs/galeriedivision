<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "admin.php";

/**
 * Class News_admin
 * @property News_model $news_model
 * @property Gallery_model $gallery_model
 * @property Artist_model $artist_model
 * @property Exhibition_model $exhibition_model
 */

class News_admin extends Admin {

    function __construct() {
        parent::__construct();
        $this->load->model("news_model");
        $this->load->model("gallery_model");
        $this->load->model("artist_model");
        $this->load->model("exhibition_model");
    }

    public function index() {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        if ($this->input->post("save")) {
            $id = $this->news_model->add($this->input->post("headline",true),$this->input->post("text",true),$this->input->post("source",true),$this->input->post("date",true),$this->input->post("url",true),$this->input->post("gallery_ids",true),$this->input->post("artist_ids",true),$this->input->post("exhibition_ids",true),$this->input->post("image_id",true));
            if ($id) {
                $error = $this->save_pdf();
            } else {
                $error = "Error saving news story.";
            }
            $this->load->view("admin/header",array("user"=>$user));
            if (!$error) {
                $this->output->append_output('<h1>Success</h1><p>News story added.</p><p><a class="button" href="/admin/news">OK</a></p>');
            } else {
                $this->output->append_output('<h1>Error</h1><p>'.$error.'</p><p><a class="button" href="/admin/news">OK</a></p>');
            }
            $this->load->view("admin/footer");
        } else {
            $news = $this->news_model->get_news();
            $this->load->view("admin/header",array("user"=>$user));
            $this->output->append_output('<h1>News</h1>');
            $this->output->append_output('<p><a class="button" href="/admin/news/create">Add news</a></p>');
            $this->load->view("admin/news_list",array("user"=>$user,"news"=>$news));
            $this->load->view("admin/footer");
        }
    }

    public function edit($id) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        if ($this->input->post("save")) {
            $error = false;
            $this->load->view("admin/header",array("user"=>$user));
            if ($this->news_model->update($id,$this->input->post("headline",true),$this->input->post("text",true),$this->input->post("source",true),$this->input->post("date",true),$this->input->post("url",true),$this->input->post("gallery_ids",true),$this->input->post("artist_ids",true),$this->input->post("exhibition_ids",true),$this->input->post("image_id",true))) {
                $error = $this->save_pdf();
            } else {
                $error = "Error saving news story.";
            }
            if (!$error) {
                $this->output->append_output('<h1>Success</h1><p>News story updated.</p><p><a class="button" href="/admin/news">OK</a></p>');
            } else {
                $this->output->append_output('<h1>Error</h1><p>'.$error.'</p><p><a class="button" href="/admin/news">OK</a></p>');
            }
            $this->load->view("admin/footer");
        } else {
            $news = $this->news_model->get_story($id);
            $galleries = $this->gallery_model->get_galleries();
            $artists = $this->artist_model->get_artists();
            $exhibitions = $this->exhibition_model->get_all_exhibitions();
            $this->load->view("admin/header",array("user"=>$user));
            $this->load->view("admin/news",array("user"=>$user,"news"=>$news,"galleries"=>$galleries,"artists"=>$artists,"exhibitions"=>$exhibitions));
            $this->load->view("admin/footer");
        }
    }

    public function create() {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header",array("user"=>$user));
        $galleries = $this->gallery_model->get_galleries();
        $artists = $this->artist_model->get_artists();
        $exhibitions = $this->exhibition_model->get_all_exhibitions();
        $this->load->view("admin/news",array("user"=>$user,"galleries"=>$galleries,"artists"=>$artists,"exhibitions"=>$exhibitions));
        $this->load->view("admin/footer");
    }

    public function delete($id) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header",array("user"=>$user));
        if (!$user["superuser"]) {
            $story = $this->news_model->get_story($id);
            reset($story["galleries"]);
            foreach ($story["galleries"] as $gallery_id) {
                if (!in_array($gallery_id, $user["galleries"])) {
                    $this->output->append_output("<h1>Error</h1><p>You are not authorized to delete this story.</p>");
                    $this->load->view("admin/footer");
                    return;
                }
            }
        }
        if (!$this->news_model->delete($id)) {
            $this->output->append_output("<h1>Error</h1><p>Error deleting story.</p>");
        } else {
            $this->output->append_output('<h1>Success</h1><p>Story deleted.</p><p><a class="button" href="/admin/news">OK</a></p>');
        }
        $this->load->view("admin/footer");
    }

    /**
     * @return bool|string <code>false</code> if file was not present or was saved without incident, otherwise error string
     */
    private function save_pdf() {
        $error = false;
        if (isset($_FILES["pdf"]) && !empty($_FILES["pdf"])) {
            switch ($_FILES["pdf"]["error"]) {
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
                    if (strtolower(path_info($_FILES["pdf"]["name"],PATHINFO_EXTENSION)) != "pdf") {
                        $error = "The uploaded file must be a PDF.";
                    } else if (!move_uploaded_file($_FILES["pdf"]["tmp_name"],rtrim(FCPATH,"/")."/pdf/".$id.".pdf")) {
                        $error = "Error moving uploaded PDF file.";
                    }
            }
        }
        return $error;
    }
}