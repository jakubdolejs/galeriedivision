<?php
if (!empty($exhibition)) {
    echo '<div class="exhibition">';
    if (!empty($exhibition["image_id"]) && !empty($image_size)) {
        echo '<p><a href="/'.$exhibition["gallery_id"].'/exhibition/'.$exhibition["id"].'"><img src="/images/'.$image_size.'/'.$exhibition["image_id"].'.jpg" alt="'.$exhibition["id"].'" class="exhibition-feature" /></a></p>';
    }
    $title = '<a href="/'.$exhibition["gallery_id"].'/exhibition/'.$exhibition["id"].'" data-exhibition_id="'.$exhibition["id"].'" class="exhibition-link">'.htmlspecialchars($exhibition["title"]).'</a>';
    $artists = "";
    if (!empty($exhibition["artists"])) {
        if (count($exhibition["artists"]) > 1) {
            $artists = array();
            foreach ($exhibition["artists"] as $artist_id=>$artist_name) {
                $artists[] = '<a href="/'.$exhibition["gallery_id"].'/artist/'.$artist_id.'">'.htmlspecialchars($artist_name).'</a>';
            }
            $artists = '<p>'.join(", ",$artists).'</p>';
        } else {
            $title = '<a class="artist exhibition-link" data-exhibition_id="'.$exhibition["id"].'" href="/'.$exhibition["gallery_id"].'/exhibition/'.$exhibition["id"].'">'.current($exhibition["artists"])."</a> ".$title;
        }
    }
    echo '<h2>'.$title.'</a></h2>'.$artists;
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
}
?>