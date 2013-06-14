<?php
$this->lang->load("common");
$class = "left";
foreach ($galleries as $gallery) {
    echo '<div class="large-column '.$class.'">';
    $class = $class == "right" ? "left" : "right";
    if (!empty($exhibitions[$gallery["id"]])) {
        $first_exhibition = current($exhibitions[$gallery["id"]]);
        if ($first_exhibition["image_id"]) {
            $exhibition_images = array();
            foreach ($exhibitions[$gallery["id"]] as $exh) {
                if ($exh["image_id"]) {
                    $exhibition_images[] = $exh["image_id"];
                }
            }
            $exhibition_images = array_unique($exhibition_images);
            echo '<a class="feature" data-image_ids="['.join(",",$exhibition_images).']" href="/'.$gallery["id"].'/exhibitions"><img class="feature" src="/images/w440/'.$first_exhibition["image_id"].'.jpg" alt="image" /></a>';
        }
        echo '<h1><a href="/'.$gallery["id"].'/exhibitions">'.$this->lang->line($gallery["city"]).'</a></h1>';
        foreach ($exhibitions[$gallery["id"]] as $exhibition) {
            $this->load->view("exhibition_listing",array("exhibition"=>$exhibition));
        }
    }
    echo '</div>';
}
?>