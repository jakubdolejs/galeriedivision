<?php
if (!empty($story)) {
    echo '<h1>'.$story["headline"].'</h1>';
    if (!empty($story["source"])) {
        echo '<h3>'.htmlspecialchars($story["source"]).'</h3>';
    }
    if (!empty($story["image_id"]) && !empty($story["text"])) {
        echo '<div class="image" data-id="'.$story["id"].'"><img src="/images/185/'.$story["image_id"].'.jpg" alt="image" /><div>'.$story["text"].'</div></div>';
    } else if (!empty($story["text"])) {
        echo '<div>'.$story["text"].'</div>';
    } else if (!empty($story["image_id"])) {
        echo '<div class="image" data-id="'.$story["id"].'"><img src="/images/440x235/'.$story["image_id"].'.jpg" alt="image" /></div>';
    }
    $links = array();
    if (!empty($story["url"])) {
        $link = '<a href="'.$story["url"].'">';
        if ($story["source"]) {
            $link .= htmlspecialchars($story["source"]);
        } else {
            $link .= htmlspecialchars($story["url"]);
        }
        $link .= '</a>';
        $links[] = $link;
    }
    if (!empty($story["exhibitions"])) {
        foreach ($story["exhibitions"] as $id=>$exhibition) {
            $links[] = '<a href="/'.$gallery_id.'/exhibition/'.$id.'">'.htmlspecialchars($exhibition).'</a>';
        }
    }
    if (!empty($story["artists"])) {
        foreach ($story["artists"] as $id=>$artist) {
            $links[] = '<a href="/'.$gallery_id.'/artist/'.$id.'">'.htmlspecialchars($artist).'</a>';
        }
    }
    if (!empty($links)) {
        echo '<h4 class="news links">Links</h4><ul class="news links"><li>'.join('</li><li>',$links).'</li></ul>';
    }
}