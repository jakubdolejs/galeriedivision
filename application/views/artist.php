<?php
if (!empty($artists)) {
    $nonrepresented_header_echoed = FALSE;
    echo '<h1>'.$this->lang->line("Artists").'</h1><ul>';
    foreach ($artists as $artist) {
        if (!$artist["represented"] && !$nonrepresented_header_echoed) {
            $nonrepresented_header_echoed = TRUE;
            echo '</ul><h3>'.$this->lang->line("Also showing works by").'</h3><ul>';
        }
        echo '<li><a href="/'.$gallery_id.'/artist/'.$artist["id"].'">'.htmlspecialchars($artist["name"]).'</a></li>';
    }
    echo '</ul>';
}