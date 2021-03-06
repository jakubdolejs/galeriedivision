<?php
$languages = array("en"=>"English","fr"=>"Français");
$lang = $this->config->item("language");
$city = "";
$nav_links = array();
$path = parse_url($this->input->server("REQUEST_URI"), PHP_URL_PATH);
$path_elements = explode("/", trim($path,"/ "));
$other_city_url = "exhibitions";
if (!empty($gallery_id)) {
    if ($path_elements[0] == $gallery_id && count($path_elements) > 1) {
        $other_city_url = $path_elements[1];
    }
    if (!empty($galleries)) {
        $nav_links = array(
            "/exhibitions"=>$this->lang->line("Exhibitions"),
            "/artists"=>$this->lang->line("Artists"),
            "/news"=>$this->lang->line("News"),
            "/contact"=>$this->lang->line("Contact"),
            //"/about"=>$this->lang->line("About")
        );
        $gallery_nav_links = array();
        foreach ($nav_links as $k=>$v) {
            $gallery_nav_links["/".$gallery_id.$k] = $v;
        }
        foreach ($galleries as $gallery) {
            if ($gallery["id"] == $gallery_id) {
                $city = $this->lang->line($gallery["city"]);
            } else {
                $gallery_nav_links["/".$gallery["id"]."/".$other_city_url] = $this->lang->line($gallery["city"]);
            }
        }
        $nav_links = $gallery_nav_links;
        foreach ($languages as $lang_code=>$lang_name) {
            if ($lang_code != $lang) {
                $getvars = $this->input->get(NULL,TRUE);
                $getvars["language"] = $lang_code;
                $nav_links[$path.'?'.http_build_query($getvars)] = $lang_name;
            }
        }
    }
} else {
    foreach ($languages as $lang_code=>$lang_name) {
        if ($lang_code != $lang) {
            $getvars = $this->input->get(NULL,TRUE);
            $getvars["language"] = $lang_code;
            $nav_links[$path.'?'.http_build_query($getvars)] = $lang_name;
        }
    }
}
$gallery_name = $this->lang->line("Division Gallery");
if ($city) {
    $gallery_name .= " ".$city;
}
$title = $title ? $gallery_name." – ".htmlspecialchars($title) : $gallery_name;
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
    <head profile="http://www.w3.org/2005/10/profile">
        <title><?php echo $title; ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
        <!--[if lte IE 8]>
        <script src="/js/html5.js"></script>
        <![endif]-->
        <link rel="shortcut icon" type="image/x-icon" href="/images/favicon.ico" />
        <link rel="icon" type="image/x-icon" href="/images/favicon.ico" />
        <link rel="stylesheet" href="/css/style.css?v=<?php echo $this->config->item("version"); ?>" type="text/css" />
        <script type="text/javascript" src="/js/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="/js/respond.min.js"></script>
        <script type="text/javascript" src="/js/main.js?v=<?php echo $this->config->item("version"); ?>"></script>
    </head>
    <body>
        <div id="content">
        <header itemscope itemtype="http://schema.org/Place" id="gallery-address" itemprop="location">
            <h1><a href="/" itemprop="name"><?php echo $this->lang->line("Division Gallery"); ?></a> <span itemprop="address" itemscope itemtype="PostalAddress"><span itemprop="addressLocality"><?php echo $city; ?></span></span></h1>
            <nav>
                <ul>
                    <?php
                    foreach ($nav_links as $url=>$title) {
                        $class = "";
                        if (!empty($gallery_id) && "/".$gallery_id."/".$other_city_url == $url) {
                            $class = ' class="selected"';
                        }
                        echo '<li'.$class.'><a href="'.$url.'">'.$title.'</a></li>';
                    }
                    ?>
                </ul>
            </nav>
            <?php if (!empty($nav_links)) { ?>
            <a class="button" id="menuButton" href="javascript:void(0)">Menu</a>
            <?php } ?>
        </header>