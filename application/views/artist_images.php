<?php
if (!empty($artist) && !empty($images) && !empty($gallery_id) && !empty($lang)) {
    echo '<ul class="thumbnails">';
    foreach ($images as $image) {
        echo '<li>
        <div><a href="/'.$gallery_id.'/artist/'.$artist["id"].'/image/'.$image["id"].'"><img class="thumbnail" src="/images/185/'.$image["id"].($image["version"] ? "-".$image["version"] : "").'.jpg" alt="image" /></a></div>';
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
        if (count($image["artists"]) > 1) {
            foreach ($image["artists"] as $id=>$image_artist) {
                if ($id != $artist["id"]) {
                    $artists[$id] = '<a href="/'.$gallery_id.'/artist/'.$id.'">'.htmlspecialchars($image_artist).'</a>';
                }
            }
            if (!empty($artists)) {
                echo "<p>".$artist["name"]." with ".join(", ",$artists).'</p>';
            }
        }
        echo '</li>';
    }
    echo '</ul>';
}