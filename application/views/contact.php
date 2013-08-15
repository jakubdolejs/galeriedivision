<h1><?php echo $this->lang->line("Contact"); ?></h1>
<?php
$this->load->view("subscribe");
?>
<div class="contact" itemscope itemtype="http://schema.org/LocalBusiness">
    <meta itemprop="name" content="<?php echo $this->lang->line("Division Gallery"); ?>" />
    <div class="column">
        <div itemscope itemtype="PostalAddress" itemprop="address">
            <h2><?php echo $this->lang->line("Address"); ?></h2>
            <div itemprop="streetAddress"><?php echo $info["address"]; ?></div>
            <div><span itemprop="addressLocality"><?php echo $info["city"]; ?></span>, <span class="small-caps" itemprop="addressRegion"><?php echo $info["province"]; ?></span></div>
            <div class="small-caps" itemprop="postalCode"><?php echo $info["postal_code"]; ?></div>
            <div>Tel. <a href="tel:<?php echo $info["telephone"]; ?>" itemprop="telephone"><?php echo $info["telephone"]; ?></a></div>
        </div>
        <div>
            <h2><?php echo $this->lang->line("Opening Hours"); ?></h2>
            <?php
            foreach ($hours as $days=>$time) {
                echo '<div>'.$days.' '.$time.'</div>';
            }
            foreach ($hours_microdata as $hr=>$days) {
                echo '<span itemprop="openingHoursSpecification" itemscope itemtype="http://schema.org/OpeningHoursSpecification">'.$hr.''.join('',$days).'</span>';
            }
            ?>
        </div>
    </div>
    <div class="column">
        <h2><?php echo $this->lang->line("Staff"); ?></h2>
        <?php
        foreach ($staff as $person) {
            echo '<div itemprop="employee" itemscope itemtype="http://schema.org/Person"><h3 itemprop="name">'.$person["name"].'</h3>';
            echo '<div itemprop="jobTitle">'.$person["title"].'</div>';
            echo '<div><a href="mailto:'.$person["email"].'" itemprop="email">'.$person["email"].'</a></div></div>';
        }
        ?>
    </div>
    <div class="map">
        <a href="http://map.google.com?q=<?php echo $info["latitude"]; ?>,<?php echo $info["longitude"]; ?>" itemprop="map"><img src="http://maps.googleapis.com/maps/api/staticmap?center=<?php echo $info["latitude"]; ?>,<?php echo $info["longitude"]; ?>&zoom=15&size=400x300&markers=color:red%7Csize=tiny%7C<?php echo $info["latitude"]; ?>,<?php echo $info["longitude"]; ?>&sensor=false" alt="Map" /></a>
    </div>
    <span itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
        <meta itemprop="latitude" content="<?php echo $info["latitude"]; ?>" />
        <meta itemprop="longitude" content="<?php echo $info["longitude"]; ?>" />
    </span>
</div>