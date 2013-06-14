<?php
$url = "/admin/exhibitions";
$title["en"] = $title["fr"] = $text["en"] = $text["fr"] = "";
$start_date = new DateTime();
$end_date = new DateTime();
$reception_start = new DateTime();
$reception_end = new DateTime();
$gallery_options = array();
$selected_space = array();
$space_options = array("0"=>"");
$js_spaces = array();
$image_id = "";
foreach ($spaces as $space) {
    if (!array_key_exists($space["gallery_id"],$gallery_options)) {
        $gallery_options[$space["gallery_id"]] = $space["city"];
    }
    if (!array_key_exists($space["gallery_id"],$js_spaces)) {
        $js_spaces[$space["gallery_id"]] = array();
    }
    $js_spaces[$space["gallery_id"]][] = $space;
}
$selected_gallery = array(current(array_keys($gallery_options)));
usort($artists,function($a,$b){
    if ($a["name"] == $b["name"]) {
        return 0;
    }
    return $a["name"] > $b["name"] ? 1 : -1;
});
$artist_ids = array();
$artist_options = array("0"=>"");
foreach ($artists as $artist) {
    $artist_options[$artist["id"]] = $artist["name"];
}
if (!empty($exhibition)) {
    $url = "/admin/exhibition/".$exhibition["id"];
    foreach (array("en","fr") as $lang) {
        if (!empty($exhibition["title"][$lang])) {
            $title[$lang] = $exhibition["title"][$lang];
        }
        if (!empty($exhibition["text"][$lang])) {
            $text[$lang] = $exhibition["text"][$lang];
        }
    }
    $start_date = DateTime::createFromFormat("Y-m-d",$exhibition["start_date"]);
    $end_date = DateTime::createFromFormat("Y-m-d",$exhibition["end_date"]);
    $reception_start = DateTime::createFromFormat("Y-m-d H:i:s",$exhibition["reception_start"]);
    $reception_end = DateTime::createFromFormat("Y-m-d H:i:s",$exhibition["reception_end"]);
    if (!empty($exhibition["artists"])) {
        $artist_ids = array_keys($exhibition["artists"]);
        foreach ($artist_ids as $id) {
            unset($artist_options[$id]);
        }
    }
    if (!empty($exhibition["image_id"])) {
        $image_id = $exhibition["image_id"];
    }
    $current_space = current($exhibition["spaces"]);
    $selected_gallery = array($current_space["gallery_id"]);
}
foreach ($spaces as $space) {
    if ($space["gallery_id"] == $selected_gallery[0]) {
        $space_options[$space["id"]] = $space["name"];
    }
}
$this->load->helper("form");
echo form_open($url,array("method"=>"post"));
if ($image_id) {
    echo '<div id="image"><p><a class="pick-image" href="javascript:void(0)"><img src="/images/w440/'.$image_id.'.jpg" /></a></p><p><a class="remove-image" href="javascript:void(0)">Remove image</a></p></div>';
} else {
    echo '<p id="no-image"><a class="pick-image" href="javascript:void(0)">Add image</a></p>';
}
echo form_fieldset("French");
echo '<p>'.form_label("Title","title[fr]").'<br />'.form_input("title[fr]",$title["fr"]).'</p>';
echo '<p>'.form_label("Text","text[fr]").'<br />'.form_textarea("text[fr]",$text["fr"]).'</p>';
echo form_fieldset_close();
echo form_fieldset("English");
echo '<p>'.form_label("Title","title[en]").'<br />'.form_input("title[en]",$title["en"]).'</p>';
echo '<p>'.form_label("Text","text[en]").'<br />'.form_textarea("text[en]",$text["en"]).'</p>';
echo form_fieldset_close();
echo '<p>'.form_label("Start date","start_date").'<br />'.form_input("start_date",$start_date->format("Y-m-d"),'class="date"').'</p>';
echo '<p>'.form_label("End date","end_date").'<br />'.form_input("end_date",$end_date->format("Y-m-d"),'class="date"').'</p>';
$times = array();
foreach (range(0,23) as $hour) {
    $time = str_pad($hour,2,"0",STR_PAD_LEFT);
    $times[$time.":00"] = $time.":00";
    $times[$time.":30"] = $time.":30";
}
echo '<p>'.form_label("Reception","reception_start").'<br />'.form_input("reception_start",$reception_start->format("Y-m-d"),'class="date"').' '.form_dropdown("reception_starttime",$times,array($reception_start->format("H:i"))).'–'.form_dropdown("reception_endtime",$times,array($reception_end->format("H:i"))).'</p>';
if (count($user["galleries"]) > 1) {
    echo '<p>'.form_label("Gallery","gallery_id").'<br />'.form_dropdown("gallery_id",$gallery_options,$selected_gallery).'</p>';
}
echo '<p>'.form_label("Space","space_ids[]").'<div id="spaces" class="multipicker">';
if (!empty($exhibition["spaces"])) {
    foreach ($exhibition["spaces"] as $space) {
        if ($space["gallery_id"] == $selected_gallery[0]) {
            echo '<div class="item" data-id="'.$space["id"].'"><span class="name">'.htmlspecialchars($space["name"]).'</span><a class="remove" data-id="'.$space["id"].'">&times;</a></div>';
            unset($space_options[$space["id"]]);
        }
    }
}
echo form_dropdown("space_id",$space_options).'</div></p>';
echo '<p>'.form_label("Artists","artist_ids[]").'<div id="artists" class="multipicker">';
if (!empty($exhibition["artists"])) {
    foreach ($exhibition["artists"] as $id=>$name) {
        echo '<div class="item" data-id="'.$id.'"><span class="name">'.htmlspecialchars($name).'</span><a class="remove" data-id="'.$id.'">&times;</a></div>';
    }
}
echo form_dropdown("all_artists",$artist_options).'</div></p>';
foreach ($artist_ids as $artist_id) {
    echo form_hidden("artist_ids[]",$artist_id);
}
echo form_hidden("image_id",$image_id);
echo '<p>'.form_submit("save","Save").'</p>';
echo form_close();
?>
<div id="imagePicker"></div>
<script type="text/javascript" src="/js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript">
    function updateArtists() {
        $("input[name='artist_ids[]']").remove();
        $("#artists div.item").each(function(){
            $("#artists").after('<input name="artist_ids[]" value="'+$(this).attr("data-id")+'" type="hidden" />');
        });
    }
    function addArtist() {
        var name = $(this).find("option:selected").text();
        var id = $(this).val();
        if (id != "0") {
            $(this).find("option:selected").remove();
            $(this).before($('<div class="item" data-id="'+id+'"><span class="name">'+name+'</span></div>').append($('<a class="remove" data-id="'+id+'">&times;</a>').on("click",removeArtist)));
        }
        $(this).val("0");
        updateArtists();
    }
    function removeArtist() {
        var id = $(this).attr("data-id");
        var name = $(this).parents("div.item").find("span.name").text();
        $(this).parents("div.item").remove();
        var names = [];
        $("select[name='all_artists'] option").each(function(){
            names.push($(this).text());
        });
        names.push(name);
        names.sort();
        var options = $("select[name='all_artists'] option");
        var nameAdded = false;
        for (var i=0; i<names.length && i<options.length; i++) {
            if (options.eq(i).text() != names[i]) {
                options.eq(i).before($('<option></option>').attr("value",id).text(name));
                nameAdded = true;
                break;
            }
        }
        if (!nameAdded) {
            $("select[name='all_artists']").append($('<option></option>').attr("value",id).text(name));
        }
        updateArtists();
    }
    function updateSpaces() {
        $("input[name='space_ids[]']").remove();
        $("#spaces div.item").each(function(){
            $("#spaces").after('<input name="space_ids[]" value="'+$(this).attr("data-id")+'" type="hidden" />');
        });
        $("select[name='space_id']").toggle($("select[name='space_id'] option").length > 1);
    }
    function addSpace() {
        var name = $(this).find("option:selected").text();
        var id = $(this).val();
        if (id != "0") {
            $(this).find("option:selected").remove();
            $(this).before($('<div class="item" data-id="'+id+'"><span class="name">'+name+'</span></div>').append($('<a class="remove" data-id="'+id+'">&times;</a>').on("click",removeSpace)));
        }
        $(this).val("0");
        updateSpaces();
    }
    function removeSpace() {
        var id = $(this).attr("data-id");
        var name = $(this).parents("div.item").find("span.name").text();
        $(this).parents("div.item").remove();
        var names = [];
        $("select[name='space_id'] option").each(function(){
            names.push($(this).text());
        });
        names.push(name);
        names.sort();
        var options = $("select[name='space_id'] option");
        var nameAdded = false;
        for (var i=0; i<names.length && i<options.length; i++) {
            if (options.eq(i).text() != names[i]) {
                options.eq(i).before($('<option></option>').attr("value",id).text(name));
                nameAdded = true;
                break;
            }
        }
        if (!nameAdded) {
            $("select[name='space_id']").append($('<option></option>').attr("value",id).text(name));
        }
        updateSpaces();
    }
    <?php
    echo 'var spaces = '.json_encode($js_spaces).';';
    ?>
    var imagePicker = new DivisionAdmin.ImagePicker();
    function addImage() {
        $('#imagePicker').show();
        imagePicker.load($('#imagePicker'),'/api/images',function(imageId){
            $('#imagePicker').empty().hide();
            if (imageId) {
                $("input[name='image_id'], #image, #no-image").remove();
                $("form").append('<input name="image_id" type="hidden" value="'+imageId+'" />');
                $("form").prepend('<div id="image"><p><a class="pick-image" href="javascript:void(0)"><img src="/images/w440/'+imageId+'.jpg" /></a></p><p><a class="remove-image" href="javascript:void(0)">Remove image</a></p></div>');
                $('a.remove-image').on("click",removeImage);
                $('a.pick-image').on("click",addImage);
            }
        });
    }
    function removeImage() {
        $("input[name='image_id'], #image, #no-image").remove();
        $("form").prepend($('<p id="no-image"></p>').append($('<a class="pick-image" href="javascript:void(0)">Add image</a>').on("click",addImage)));
    }
    $('a.remove-image').on("click",removeImage);
    $('a.pick-image').on("click",addImage);
    $("input.date").datepicker({"dateFormat":"yy-mm-dd"});
    $("select[name='all_artists']").on("change",addArtist);
    $("#artists div.item a.remove").on("click",removeArtist);
    $("select[name='gallery_id']").on("change",function(){
        $("select[name='space_id']").empty().show().append($('<option value="0"></option>'));
        $("#spaces div.item").remove();
        var gallery_id = $(this).val();
        for (var i=0; i<spaces[gallery_id].length; i++) {
            $("select[name='space_id']").append($('<option value="'+spaces[gallery_id][i].id+'"></option>').text(spaces[gallery_id][i].name));
        }
    });
    $("select[name='space_id']").on("change",addSpace);
    $("#spaces div.item a.remove").on("click",removeSpace);
    updateSpaces();

    $("form").on("submit",function(){
        console.log($("form").get(0).elements);
    });

</script>