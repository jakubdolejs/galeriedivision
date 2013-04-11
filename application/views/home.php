<?php
$this->lang->load("common");
foreach ($galleries as $gallery) {
    echo '<div><h1><a href="/'.$gallery["id"].'/exhibitions">'.$this->lang->line($gallery["city"]).'</a></h1></div>';
}
?>