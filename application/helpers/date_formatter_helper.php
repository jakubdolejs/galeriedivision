<?php
if (!function_exists("format_exhibition_dates")) {

    function format_exhibition_dates($start,$end) {
        $dates = "";
        $CI =& get_instance();
        $CI->load->config();
        $lang = $CI->config->item("language");
        $CI->lang->load("calendar");
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
                    $dates = $start->format("j")."–".$end->format("j ").$CI->lang->line("cal_".strtolower($end->format("F"))).$end->format(", Y");
                } else {
                    $dates = $start->format("j ").$CI->lang->line("cal_".strtolower($start->format("F")))."–".$end->format("j ").$CI->lang->line("cal_".strtolower($end->format("F"))).$end->format(", Y");
                }
            } else {
                $dates = $start->format("j ").$CI->lang->line("cal_".strtolower($start->format("F"))).$start->format(", Y")."–".$end->format("j ").$CI->lang->line("cal_".strtolower($end->format("F"))).$end->format(", Y");
            }
        }
        return $dates;
    }
}

if (!function_exists("format_opening_reception_dates")) {

    function format_opening_reception_dates($start,$end) {
        $CI =& get_instance();
        $CI->load->config();
        $lang = $CI->config->item("language");
        $CI->lang->load("calendar");
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
        $reception = $start->format($date_format)." ".$CI->lang->line("from")." ".$start->format($start_time_format)." ".$CI->lang->line("to")." ".$end->format($end_time_format);
        if ($lang == "fr") {
            $reception = $CI->lang->line("weekday_".$start->format("N"))." ".$start->format("j")." ".$CI->lang->line("cal_".strtolower($start->format("F")))." ".$reception;
        }
        return $reception;
    }
}