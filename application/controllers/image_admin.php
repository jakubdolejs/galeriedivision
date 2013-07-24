<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once "admin.php";

/**
 * Class Image_admin
 * @property Image_model $image_model
 * @property Artist_model $artist_model
 */

class Image_admin extends Admin {

    function __construct() {
        parent::__construct();
        $this->load->model("image_model");
        $this->load->model("artist_model");
    }

    public function index() {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header",array("user"=>$user));
        $images = $this->image_model->get_list();
        $this->output->append_output('<h1>Images</h1>');
        $this->load->view("admin/image_upload",array("images"=>$images));
        $this->load->view("admin/footer");
    }

    public function float_check($val) {
        if (!$val) {
            return true;
        }
        return preg_match('/^\d+(\.\d+)*$/',$val) == 1;
    }

    public function edit($image_id) {
        $user = $this->get_logged_in_user();
        if (!$user) {
            redirect(site_url("/admin/login"));
            return;
        }
        $this->load->view("admin/header",array("user"=>$user));
        if ($this->input->post("save")) {
            $this->load->library("form_validation");
            $this->form_validation->set_rules('width','trim|callback_float_check');
            $this->form_validation->set_rules('height','trim|callback_float_check');
            $this->form_validation->set_rules('depth','trim|callback_float_check');
            if ($this->form_validation->run() == true) {
                $this->image_model->update($image_id,$this->input->post("width"),$this->input->post("height"),$this->input->post("depth"),$this->input->post("year"),$this->input->post("artists"),$this->input->post("title"),$this->input->post("description"));
                $this->output->append_output('<h1>Success</h1><p>Image updated</p><p><img src="/images/185/'.$image_id.'.jpg" /></p><p><a class="button" href="/admin/images">OK</a></p>');
            } else {
                $this->output->append_output('<h1>Error</h1><p>Error updating image. Please check that the dimensions are entered as numbers or left blank.</p><p><a class="button" href="/admin/image/'.$image_id.'">OK</a></p>');
            }
        } else {
            $image = $this->image_model->get($image_id);
            $artists = $this->artist_model->get_artists();
            $this->load->view("admin/image",array("image"=>$image,"artists"=>$artists));
        }
        $this->load->view("admin/footer");
    }

    private function resample($source_filename,$destination_filename,$max_width=null,$max_height=null,$crop=false) {
        $source = imagecreatefromjpeg($source_filename);
        if (!$source) {
            return false;
        }
        $src_w = $width = imagesx($source);
        $src_h = $height = imagesy($source);
        $src_x = $src_y = $dest_x = $dest_y = 0;
        $whratio = $src_w/$src_h;
        if ($max_width && $max_width == $max_height && $crop) { //crop to a square
            $width = $height = $max_width;
            if ($whratio > 1) {
                $src_x = round($src_w/2-$src_h/2);
                $src_w = $src_h;
            } else {
                $src_y = round($src_h/2-$src_w/2);
                $src_h = $src_w;
            }
        } else if ($max_width && $max_height) {
            if ($src_w/$src_h >= $max_width/$max_height) {
                $scaleRatio = $max_width/$src_w;
            } else {
                $scaleRatio = $max_height/$src_h;
            }
            $width = $src_w * $scaleRatio;
            $height = $src_h * $scaleRatio;
        } else if ($max_width && $max_width < $src_w) {
            $width = $max_width;
            $height = round($width/$whratio);
        } else if ($max_height && $max_height < $src_h) {
            $height = $max_height;
            $width = round($height*$whratio);
        }
        $destination = imagecreatetruecolor($width, $height);
        if (!$destination) {
            return false;
        }
        if ($width != $src_w || $height != $src_h) {
            imagecopyresampled($destination, $source, $dest_x, $dest_y, $src_x, $src_y, $width, $height, $src_w, $src_h);
        } else {
            imagecopy($destination, $source, $dest_x, $dest_y, $src_x, $src_y, $src_w, $src_h);
        }
        imagedestroy($source);
        imagejpeg($destination,$destination_filename,90);
        imagedestroy($destination);
        return true;
    }

    public function upload() {
        set_time_limit(0);
        ignore_user_abort(true);
        if (isset($_FILES["file"]) && !empty($_FILES["file"])) {
            $response = array();

            for ($i=0; $i<count($_FILES["file"]["tmp_name"]); $i++) {
                switch ($_FILES["file"]["error"][$i]) {
                    case UPLOAD_ERR_INI_SIZE:
                        //Value: 1; The uploaded file exceeds the upload_max_filesize directive in php.ini.
                        $response[$i] = array("error"=>"The uploaded file exceeds the maximum permitted upload size.");
                        break;
                        continue;
                    case UPLOAD_ERR_FORM_SIZE:
                        //Value: 2; The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.
                        $response[$i] = array("error"=>"The uploaded file exceeds the maximum permitted upload size.");
                        break;
                        continue;
                    case UPLOAD_ERR_PARTIAL:
                        //Value: 3; The uploaded file was only partially uploaded.
                        $response[$i] = array("error"=>"The file was only partially uploaded.");
                        break;
                        continue;
                    case UPLOAD_ERR_NO_FILE:
                        //Value: 4; No file was uploaded.
                        $response[$i] = array("error"=>"No file was uploaded.");
                        break;
                        continue;
                    case UPLOAD_ERR_NO_TMP_DIR:
                        //Value: 6; Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.
                        $response[$i] = array("error"=>"Missing a temporary folder.");
                        break;
                        continue;
                    case UPLOAD_ERR_CANT_WRITE:
                        //Value: 7; Failed to write file to disk. Introduced in PHP 5.1.0.
                        $response[$i] = array("error"=>"Failed to write the uploaded file to disk.");
                        break;
                        continue;
                    case UPLOAD_ERR_EXTENSION:
                        //Value: 8; A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help. Introduced in PHP 5.2.0.
                        $response[$i] = array("error"=>"A PHP extension stopped the file upload.");
                        break;
                        continue;
                }

                $img_file = $_FILES["file"]["tmp_name"][$i];

                $exif = @exif_read_data($img_file,NULL,TRUE);

                getimagesize($img_file, $info);

                $image_data = file_get_contents($img_file);

                $original = imagecreatefromstring($image_data);
                unset($image_data);

                if (!$original) {
                    $response[$i] = array("error"=>"Cannot read image ".$_FILES["file"]["name"][$i]);
                    continue;
                }

                $meta = array();
                $meta["original_width"] = $src_w = $w = imagesx($original);
                $meta["original_height"] = $src_h = $h = imagesy($original);
                $whratio = $src_w/$src_h;
                $original_mp = $src_w*$src_h;
                $target_mp = 2000000;
                if ($original_mp > $target_mp) {
                    $h = floor(sqrt($target_mp/($src_w/$src_h)));
                    $w = round($h*$whratio);
                }
                $src_x = $src_y = 0;
                $rotation = 0;
                if (isset($exif['IFD0']['Orientation'])) {
                    switch ($exif['IFD0']['Orientation']) {
                        case 2:
                            //horizontal flip
                            $src_x = $src_w-1;
                            $src_w = 0-$src_w;
                            break;
                        case 3:
                            $rotation = 180;
                            break;
                        case 4:
                            //vertical flip
                            $src_y = $src_h-1;
                            $src_h = 0-$src_h;
                            break;
                        case 5:
                            //vertical flip
                            $meta["original_width"] = $src_h;
                            $meta["original_height"] = $src_w;
                            $src_y = $src_h-1;
                            $src_h = 0-$src_h;
                            $rotation = -90;
                            break;
                        case 6:
                            $meta["original_width"] = $src_h;
                            $meta["original_height"] = $src_w;
                            $rotation = -90;
                            break;
                        case 7:
                            //horizontal flip
                            $meta["original_width"] = $src_h;
                            $meta["original_height"] = $src_w;
                            $src_x = $src_w-1;
                            $src_w = 0-$src_w;
                            $rotation = -90;
                            break;
                        case 8:
                            $meta["original_width"] = $src_h;
                            $meta["original_height"] = $src_w;
                            $rotation = 90;
                            break;

                    }

                }
                $image_id = $this->image_model->insert($meta["original_width"],$meta["original_height"]);

                if (!$image_id) {
                    $response[$i] = array("error"=>"Unable to insert image ".$_FILES["file"]["name"][$i]." to the database");
                    imagedestroy($original);
                    continue;
                }

                $images_dir = rtrim(FCPATH,"/")."/images";

                if (!move_uploaded_file($img_file,$images_dir."/original/".$image_id.".".pathinfo($_FILES["file"]["name"][$i],PATHINFO_EXTENSION))) {
                    $response[$i] = array("error"=>"Unable to save uploaded file");
                    $this->image_model->delete($image_id);
                    continue;
                }

                $target = imagecreatetruecolor($w,$h);
                $scale = $w/$src_w;
                if ($w != $src_w) {
                    imagecopyresampled($target, $original, 0, 0, $src_x, $src_y, $w, $h, $src_w, $src_h);
                } else {
                    imagecopy($target, $original, 0, 0, $src_x, $src_y, $w, $h);
                }

                if ($rotation != 0) {
                    $target = imagerotate($target, $rotation, 0);
                    $original = imagerotate($original,$rotation,0);
                }
                $large_file = $images_dir."/2mp/".$image_id.".jpg";
                imagejpeg($target, $large_file, 90);
                imagedestroy($target);

                if ($this->input->post("crop")) {
                    $crop = $this->input->post("crop",true);
                    $crop = $crop[$i];
                    if (is_string($crop)) {
                        $crop = explode(",",$crop);
                    }

                    $w400h235 = imagecreatetruecolor(440,235);
                    imagecopyresampled($w400h235, $original, 0, 0, $crop[0], $crop[1], 440, 235, $crop[2], $crop[3]);
                    imagejpeg($w400h235, $images_dir."/440x235/".$image_id.".jpg");
                    imagedestroy($w400h235);

                    $w900h480 = imagecreatetruecolor(900,480);
                    imagecopyresampled($w900h480, $original, 0, 0, $crop[0], $crop[1], 900, 480, $crop[2], $crop[3]);
                    imagejpeg($w900h480, $images_dir."/900x480/".$image_id.".jpg");
                    imagedestroy($w900h480);
                }
                imagedestroy($original);

                $files = array(
                    /*$images_dir."/w900/".$image_id.".jpg"=>array(900,null),
                    $images_dir."/w440/".$image_id.".jpg"=>array(440,null),*/
                    $images_dir."/185/".$image_id.".jpg"=>array(185,185)
                );

                foreach ($files as $file=>$params) {
                    if (!$this->resample($large_file,$file,$params[0],$params[1])) {
                        $response[$i] = array("error"=>"Error resampling file ".$_FILES["file"]["name"][$i]);
                        $this->image_model->delete($image_id);
                        break;
                        continue;
                    }
                }
                $response[$i] = array("id"=>$image_id);
            }
        } else {
            $response = array("error"=>"No files submitted");
        }
        $this->load->view("json",array("data"=>$response));
    }
}