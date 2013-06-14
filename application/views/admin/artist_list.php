<?php
if (!empty($artists)) {
    usort($artists,function($a,$b){
        if ($a["name"] == $b["name"]) {
            return 0;
        }
        return $a["name"] > $b["name"] ? 1 : -1;
    });
    echo '<table><tbody>';
    foreach ($artists as $artist) {
        echo '<tr><td><a href="/admin/artist/'.$artist["id"].'">'.$artist["name"].'</a></td>';
        if (!empty($artist["galleries"]) && !$user["superuser"]) {
            foreach (array_keys($artist["galleries"]) as $gallery) {
                if (!in_array($gallery,$user["galleries"])) {
                    echo '</tr>';
                    continue 2;
                }
            }
        }
        echo '<td><a class="deleteLink" href="/admin/artist/'.$artist["id"].'/delete">delete</a></td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
}
?>
<script type="text/javascript">
    $("a.deleteLink").on("click",function(){
        return confirm("Are you sure you want to delete "+$(this).parents("tr").first().find("td").first().text()+"?");
    });
</script>