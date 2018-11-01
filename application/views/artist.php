<?php
if (!empty($artists)) {
    $represented = array();
    $available = array();
    foreach ($artists as $artist) {
        if ($artist["galleries"][$gallery_id]["represented"]) {
            $represented[] = $artist;
        } else {
            $available[] = $artist;
        }
    }
    echo '<h1>'.$this->lang->line("Artists").'</h1><ul class="thumbnails">';
    foreach ($represented as $artist) {
        echo '<li itemscope itemtype="http://schema.org/Person">';
        if (!empty($artist["galleries"][$gallery_id]["image_id"])) {
            echo '<div><a href="https://'.$this->input->server("HTTP_HOST").'/'.$gallery_id.'/artist/'.$artist["id"].'" itemprop="url"><img itemprop="image" class="thumbnail" src="https://'.$this->input->server("HTTP_HOST").'/images/185/'.$artist["galleries"][$gallery_id]["image_id"].'.jpg" alt="image" /></a></div>';
        }
        echo '<a class="artist" href="https://'.$this->input->server("HTTP_HOST").'/'.$gallery_id.'/artist/'.$artist["id"].'" itemprop="name">'.htmlspecialchars($artist["name"]).'</a></li>';
    }
    if (!empty($available)) {
        echo '</ul><h1>'.$this->lang->line("Also showing works by").'</h1><ul class="thumbnails">';
        foreach ($available as $artist) {
            echo '<li itemscope itemtype="http://schema.org/Person">';
            if (!empty($artist["galleries"][$gallery_id]["image_id"])) {
                echo '<div><a href="https://'.$this->input->server("HTTP_HOST").'/'.$gallery_id.'/artist/'.$artist["id"].'" itemprop="url"><img itemprop="image" class="thumbnail" src="https://'.$this->input->server("HTTP_HOST").'/images/185/'.$artist["galleries"][$gallery_id]["image_id"].'.jpg" alt="image" /></a></div>';
            }
            echo '<a class="artist" href="https://'.$this->input->server("HTTP_HOST").'/'.$gallery_id.'/artist/'.$artist["id"].'" itemprop="name">'.htmlspecialchars($artist["name"]).'</a></li>';
        }
        echo '</ul>';
    }
}