<h1><?php echo $this->lang->line("Contact"); ?></h1>
<div class="contact" itemscope itemtype="http://schema.org/LocalBusiness">
    <meta itemprop="name" content="<?php echo $this->lang->line("Division Gallery"); ?>" />
    <div class="column gallery">
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
        <h2><?php echo $this->lang->line("Staff"); ?></h2>
        <?php
        foreach ($staff as $person) {
            echo '<div itemprop="employee" itemscope itemtype="http://schema.org/Person"><h3 itemprop="name">'.$person["name"].'</h3>';
            echo '<div itemprop="jobTitle">'.$person["title"].'</div>';
            echo '<div><a href="mailto:'.$person["email"].'" itemprop="email">'.$person["email"].'</a></div></div>';
        }
        ?>
        <div><?php echo $this->lang->line("The gallery is not currently accepting artist submissions."); ?></div>
    </div>
    <div class="column">
        <?php
        $this->load->view("subscribe",array("lists"=>$lists));
        ?>
    </div>
    <div class="map">
        <div></div>
    </div>
    <span itemprop="geo" itemscope itemtype="http://schema.org/GeoCoordinates">
        <meta itemprop="latitude" content="<?php echo $info["latitude"]; ?>" />
        <meta itemprop="longitude" content="<?php echo $info["longitude"]; ?>" />
    </span>
</div>
<script src="//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script type="text/javascript">
    //<![CDATA[
    function initialize() {
        var gallery = new google.maps.LatLng(<?php echo $info["latitude"]; ?>, <?php echo $info["longitude"]; ?>);
        var mapOptions = {
            zoom: 15,
            center: gallery,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        var map = new google.maps.Map($('div.map div').get(0),
            mapOptions);
        var marker = new google.maps.Marker({
            position: gallery,
            map: map
        });
    }
    google.maps.event.addDomListener(window, 'load', initialize);
    //]]>
</script>