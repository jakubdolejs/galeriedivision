<?php
if (!empty($artists)) {
    $nonrepresented_header_echoed = FALSE;
    echo '<h1>'.$this->lang->line("Artists").'</h1><ul class="thumbnails">';
    foreach ($artists as $artist) {
        if (!$artist["galleries"][$gallery_id]["represented"] && !$nonrepresented_header_echoed) {
            $nonrepresented_header_echoed = TRUE;
            echo '</ul><h1>'.$this->lang->line("Also showing works by").'</h1><ul>';
        }
        echo '<li>';
        if (!empty($artist["galleries"][$gallery_id]["image_id"])) {
            echo '<div><a href="/'.$gallery_id.'/artist/'.$artist["id"].'"><img class="thumbnail" src="/images/'.$artist["galleries"][$gallery_id]["image_id"].'.jpg" alt="image" /></a></div>';
        }
        echo '<a class="artist" href="/'.$gallery_id.'/artist/'.$artist["id"].'">'.htmlspecialchars($artist["name"]).'</a></li>';
    }
    echo '</ul>';
}