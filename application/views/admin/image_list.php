<?php
if (!empty($images)) {
    echo '<ul class="thumbnails">';
    foreach ($images as $image) {
        echo '<li><a href="/admin/image/'.$image["id"].'"><img src="/images/185/'.$image["id"].'.jpg" alt="" /></a>';
        if (!empty($image["artists"])) {
            echo '<p>'.htmlspecialchars(join(", ",array_values($image["artists"]))).'</p>';
        }
        echo '</li>';
    }
    echo '</ul>';
}