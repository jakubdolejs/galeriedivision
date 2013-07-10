<?php
if (!empty($artist) && !empty($lang)) {
    if (!empty($artist["cv"][$lang])) {
        echo $artist["cv"][$lang];
    } else if (!empty($artist["cv"])) {
        echo current($artist["cv"]);
    }
}