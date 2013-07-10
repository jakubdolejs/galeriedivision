<?php
if (!empty($exhibition) && !empty($gallery_id) && !empty($lang)) {
    echo '<div class="exhibition">';
    if (!empty($exhibition["image_id"])) {
        echo '<p><img src="/images/900x480/'.$exhibition["image_id"].'.jpg" alt="'.$exhibition["id"].'" class="exhibition-feature" /></p>';
    }
    if (!empty($exhibition["title"][$lang])) {
        $title = htmlspecialchars($exhibition["title"]);
    } else {
        $title = "";
    }
    $artists = "";
    if (!empty($exhibition["artists"])) {
        if (count($exhibition["artists"]) > 1) {
            $artists = array();
            foreach ($exhibition["artists"] as $artist_id=>$artist_name) {
                $artists[] = '<a href="/'.$exhibition["gallery_id"].'/artist/'.$artist_id.'">'.htmlspecialchars($artist_name).'</a>';
            }
            $artists = '<p>'.join(", ",$artists).'</p>';
        } else {
            $title = htmlspecialchars(current($exhibition["artists"]))." ".$title;
        }
    }
    echo '<h2>'.$title.'</h2>'.$artists;
    $start = DateTime::createFromFormat("Y-m-d", $exhibition["start_date"]);
    $end = DateTime::createFromFormat("Y-m-d", $exhibition["end_date"]);
    $this->load->helper("date_formatter");
    $dates = format_exhibition_dates($start,$end);
    echo '<p>'.$dates.'</p>';
    if ($exhibition["reception_start"] && $exhibition["reception_end"]) {
        $end = DateTime::createFromFormat("Y-m-d H:i:s", $exhibition["reception_end"]);
        if ($end->getTimestamp() >= time()) {
            $start = DateTime::createFromFormat("Y-m-d H:i:s", $exhibition["reception_start"]);
            $reception = format_opening_reception_dates($start,$end);
            echo '<p>'.$this->lang->line("Opening reception")." ".$reception.'</p>';
        }
    }
    echo '</div>';
    if (!empty($images)) {
        $this->lang->load("common");
        $base_image_url = $artist_id ? "/".$gallery_id."/artist/".$artist_id."/exhibition/".$exhibition["id"]."/image/" : "/".$gallery_id."/exhibition/".$exhibition["id"]."/image/";
        echo '<h3>'.$this->lang->line("Works in the exhibition").'</h3><ul class="thumbnails">';
        foreach ($images as $image) {
            echo '<li>
        <div><a href="'.$base_image_url.$image["id"].'"><img class="thumbnail" src="/images/185/'.$image["id"].'.jpg" alt="image" /></a></div>';
            $artists = array();
            if (!empty($image["title"][$lang])) {
                echo '<p class="title">'.htmlspecialchars($image["title"][$lang]).'</p>';
            } else if (!empty($image["title"])) {
                foreach ($image["title"] as $language=>$title) {
                    if (!empty($title)) {
                        echo '<p class="title" lang="'.$language.'">'.htmlspecialchars($title).'</p>';
                        break;
                    }
                }
            }
            $exhibition_artist_keys = "";
            if (!empty($exhibition["artists"])) {
                $exhibition_artist_keys = array_keys($exhibition["artists"]);
                sort($exhibition_artist_keys);
                $exhibition_artist_keys = join(",",$exhibition_artist_keys);
            }
            if (!empty($image["artists"])) {
                $image_artists = array_keys($image["artists"]);
                sort($image_artists);
                if (join(",",$image_artists) != $exhibition_artist_keys) {
                    $image_artists = array();
                    foreach ($image["artists"] as $id=>$artist) {
                        $image_artists[] = '<a href="/'.$gallery_id.'/artist/'.$id.'">'.htmlspecialchars($artist).'</a>';
                    }
                    echo "<p>".join(", ",$image_artists).'</p>';
                }
            }
            echo '</li>';
        }
        echo '</ul>';
    }
    if (!empty($exhibition["text"][$lang])) {
        echo '<div>'.$exhibition["text"][$lang].'</div>';
    } else if (!empty($exhibition["text"])) {
        foreach ($exhibition["text"] as $language=>$text) {
            if (!empty($text)) {
                echo '<div lang="'.$language.'">'.$text.'</div>';
                break;
            }
        }
    }
}