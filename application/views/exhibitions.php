<?php
$this->lang->load("common");
$this->load->config();
$lang = $this->config->item("language");
$this->load->helper("date_formatter");
echo '<div class="exhibitions">';
if (!empty($current)) {
    echo '<h1>'.$this->lang->line("Current Exhibitions").'</h1>';
    foreach ($current as $exhibition) {
        $this->load->view("exhibition_listing",array("exhibition"=>$exhibition,"image_size"=>"900x480"));
    }
}
if (!empty($upcoming)) {
    echo '<h1>'.$this->lang->line("Upcoming Exhibitions").'</h1>';
    foreach ($upcoming as $exhibition) {
        $this->load->view("exhibition_listing",array("exhibition"=>$exhibition,"image_size"=>"900x480"));
    }
}
if (!empty($past)) {
    echo '<p><a href="/'.$gallery_id.'/past_exhibitions">'.$this->lang->line("Past Exhibitions").'</a></p>';
}
echo '</div>';