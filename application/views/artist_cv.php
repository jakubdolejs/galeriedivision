<?php
if (!empty($artist)) {
    $filename = false;
    if (file_exists(rtrim(FCPATH,"/")."/cv_pdf/".$artist["id"]."-".$lang.".pdf")) {
        $filename = "/cv_pdf/".$artist["id"]."-".$lang.".pdf";
    } else {
        $languages = array("en","fr");
        foreach ($languages as $language) {
            if ($language != $lang && file_exists(rtrim(FCPATH,"/")."/cv_pdf/".$artist["id"]."-".$language.".pdf")) {
                $filename = "/cv_pdf/".$artist["id"]."-".$language.".pdf";
                break;
            }
        }
    }
    if ($filename) {
        echo '<object class="cv" data="'.$filename.'" type="application/pdf" width="100%" height="100%"><a href="'.$filename.'">Download PDF</a></object>';
    }
}