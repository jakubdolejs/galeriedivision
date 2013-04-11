<?php
$this->lang->load("common");
$languages = array("en"=>"English","fr"=>"Français");
$lang = $this->config->item("language");
$path = parse_url($this->input->server("REQUEST_URI"), PHP_URL_PATH);
$path_elements = explode("/", trim($path,"/ "));
$other_city_url = "exhibitions";
if ($path_elements[0] == $gallery_id && count($path_elements) > 1) {
    $other_city_url = $path_elements[1];
}
/*
$lang_links = array();
foreach ($languages as $lang_code=>$lang_name) {
    if ($lang_code == $lang) {
        $lang_links[] = $lang_name;
    } else {
        $getvars = $this->input->get(NULL,TRUE);
        $getvars["language"] = $lang_code;
        $lang_links[] = '<a href="'.$path.'?'.http_build_query($getvars).'">'.$lang_name.'</a>';
    }
}
*/
$nav_links = array(
    "/exhibitions"=>$this->lang->line("Exhibitions"),
    "/artists"=>$this->lang->line("Artists"),
    "/news"=>$this->lang->line("News"),
    "/contact"=>$this->lang->line("Contact"),
    "/about"=>$this->lang->line("About")
);
$city = "";
if (!empty($gallery_id) && !empty($galleries)) {
    $gallery_nav_links = array();
    foreach ($nav_links as $k=>$v) {
        $gallery_nav_links["/".$gallery_id.$k] = $v;
    }
    foreach ($galleries as $gallery) {
        if ($gallery["id"] == $gallery_id) {
            $city = " ".$this->lang->line($gallery["city"]);
        } else {
            $gallery_nav_links["/".$gallery["id"]."/".$other_city_url] = $this->lang->line($gallery["city"]);
        }
    }
    $nav_links = $gallery_nav_links;
}
foreach ($languages as $lang_code=>$lang_name) {
    if ($lang_code != $lang) {
        $getvars = $this->input->get(NULL,TRUE);
        $getvars["language"] = $lang_code;
        $nav_links[$path.'?'.http_build_query($getvars)] = $lang_name;
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
    <head>
        <title></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <header>
            <h1><?php echo $this->lang->line("Division Gallery").$city; ?></h1>
            <nav>
                <ul>
                    <?php
                    foreach ($nav_links as $url=>$title) {
                        echo '<li><a href="'.$url.'">'.$title.'</a></li>';
                    }
                    ?>
                </ul>
            </nav>
        </header>