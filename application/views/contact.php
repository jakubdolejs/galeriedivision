<h1><?php echo $this->lang->line("Contact"); ?></h1>
<?php
echo '<div class="subscribe">';
$this->load->view("subscribe");
echo '</div>';
?>
<div class="contact">
    <div class="column">
        <div>
            <h2><?php echo $this->lang->line("Address"); ?></h2>
            <div><?php echo $info["address"]; ?></div>
            <div><?php echo $info["city"]; ?>, <span class="small-caps"><?php echo $info["province"]; ?></span></div>
            <div class="small-caps"><?php echo $info["postal_code"]; ?></div>
            <div>Tel. <a href="tel:<?php echo $info["telephone"]; ?>"><?php echo $info["telephone"]; ?></a></div>
        </div>
        <div>
            <h2><?php echo $this->lang->line("Opening Hours"); ?></h2>
            <?php
            foreach ($hours as $days=>$time) {
                echo '<div>'.$days.' '.$time.'</div>';
            }
            ?>
        </div>
    </div>
    <div class="column">
        <h2><?php echo $this->lang->line("Staff"); ?></h2>
        <?php
        foreach ($staff as $person) {
            echo '<h3>'.$person["name"].'</h3>';
            echo '<div>'.$person["title"].'</div>';
            echo '<div><a href="mailto:'.$person["email"].'">'.$person["email"].'</a></div>';
        }
        ?>
    </div>
    <div class="map">
        <a href="http://map.google.com?q=<?php echo $info["latitude"]; ?>,<?php echo $info["longitude"]; ?>"><img src="http://maps.googleapis.com/maps/api/staticmap?center=<?php echo $info["latitude"]; ?>,<?php echo $info["longitude"]; ?>&zoom=15&size=400x300&markers=color:red%7Csize=tiny%7C<?php echo $info["latitude"]; ?>,<?php echo $info["longitude"]; ?>&sensor=false" alt="Map" /></a>
    </div>
</div>