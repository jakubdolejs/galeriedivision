<?php
$this->lang->load("common");
$this->load->config();
$lang = $this->config->item("language");
if (!empty($current)) {
    echo '<h1>'.$this->lang->line("Current Exhibitions").'</h1>';
    foreach ($current as $exhibition) {
        echo '<div>';
        if ($exhibition["image_id"]) {
            echo '<p><img src="/image/'.$exhibition["image_id"].'" /></p>';
        }
        echo '<h1>'.$exhibition["title"].'</h1>';
        if (!empty($exhibition["artists"])) {
            $artist_links = array();
            foreach ($exhibition["artists"] as $artist_id=>$artist_name) {
                $artist_links[] = '<a href="/'.$gallery_id.'/artist/'.$artist_id.'">'.$artist_name.'</a>';
            }
            echo '<p>'.join(", ",$artist_links).'</p>';
        }
        $start = DateTime::createFromFormat("Y-m-d", $exhibition["start_date"]);
        $end = DateTime::createFromFormat("Y-m-d", $exhibition["end_date"]);
        $dates = "";
        $this->lang->load("calendar");
        if ($lang == "en") {
            if ($start->format("Y") == $end->format("Y")) {
                if ($start->format("n") == $end->format("n")) {
                    $dates = $start->format("F j")."–".$end->format("j, Y");
                } else {
                    $dates = $start->format("F j")."–".$end->format("F j, Y");
                }
            } else {
                $dates = $start->format("F j, Y")."–".$end->format("F j, Y");
            }
        } else {
            if ($start->format("Y") == $end->format("Y")) {
                if ($start->format("n") == $end->format("n")) {
                    $dates = $start->format("j")."–".$end->format("j ").$this->lang->line("cal_".strtolower($end->format("F"))).$end->format(", Y");
                } else {
                    $dates = $start->format("j ").$this->lang->line("cal_".strtolower($start->format("F")))."–".$end->format("j ").$this->lang->line("cal_".strtolower($end->format("F"))).$end->format(", Y");
                }
            } else {
                $dates = $start->format("j ").$this->lang->line("cal_".strtolower($start->format("F"))).$start->format(", Y")."–".$end->format("j ").$this->lang->line("cal_".strtolower($end->format("F"))).$end->format(", Y");
            }
        }
        echo '<p>'.$dates.'</p>';
        if ($exhibition["reception_start"] && $exhibition["reception_end"]) {
            $start = DateTime::createFromFormat("Y-m-d H:i:s", $exhibition["reception_start"]);
            $end = DateTime::createFromFormat("Y-m-d H:i:s", $exhibition["reception_end"]);
            $start_time_format = $lang == "en" ? "g" : 'G\h';
            if ($start->format("i") != "00") {
                $start_time_format .= ":i";
            }
            if ($lang == "en" && $start->format("a") != $end->format("a")) {
                $start_time_format .= "a";
            }
            $end_time_format = $lang == "en" ? "g" : 'G\h';
            if ($end->format("i") != "00") {
                $end_time_format .= ":i";
            }
            if ($lang == "en") {
                $end_time_format .= "a";
            }
            $date_format = $lang == "en" ? "l F j " : "";
            $reception = $start->format($date_format)." ".$this->lang->line("from")." ".$start->format($start_time_format)." ".$this->lang->line("to")." ".$end->format($end_time_format);
            if ($lang == "fr") {
                $reception = $this->lang->line("weekday_".$start->format("N"))." ".$start->format("j")." ".$this->lang->line("cal_".strtolower($start->format("F")))." ".$reception;
            }
            echo '<p>'.$this->lang->line("Opening reception")." ".$reception.'</p>';
        }
        echo '</div>';
    }
}
if (!empty($upcoming)) {
    echo '<h1>'.$this->lang->line("Upcoming Exhibitions").'</h1>';
}
if (!empty($past)) {
    echo '<p><a href="/exhibitions/'.$gallery_id.'/past">'.$this->lang->line("Past Exhibitions").'</a></p>';
}