<?php
$url = "/admin/exhibitions";
$title["en"] = $title["fr"] = $text["en"] = $text["fr"] = "";
$start_date = new DateTime();
$end_date = new DateTime();
$reception_start = new DateTime();
$reception_end = new DateTime();
$gallery_options = array();
$selected_space = array();
$space_options = array();
$js_spaces = array();
$space_ids = array();
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
$artist_options = array();
foreach ($artists as $artist) {
    $artist_options[] = array("id"=>$artist["id"],"text"=>$artist["name"]);
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
    }
    if (!empty($exhibition["image_id"])) {
        $image_id = $exhibition["image_id"];
    }
    $current_space = current($exhibition["spaces"]);
    $selected_gallery = array($current_space["gallery_id"]);
    if (!empty($exhibition["spaces"])) {
        foreach ($exhibition["spaces"] as $space) {
            if ($space["gallery_id"] == $selected_gallery[0]) {
                $space_ids[] = $space["id"];
            }
        }
    }
}
foreach ($spaces as $space) {
    if ($space["gallery_id"] == $selected_gallery[0]) {
        $space_options[] = array("id"=>$space["id"],"text"=>$space["name"]);
    }
}
$this->load->helper("form");
echo form_open($url,array("method"=>"post"));
if ($image_id) {
    echo '<div id="image"><p><a class="pick-image" href="javascript:void(0)"><img src="/images/440x235/'.$image_id.'.jpg" /></a></p><p><a class="remove-image" href="javascript:void(0)">Remove image</a></p></div>';
} else {
    echo '<p id="no-image"><a class="pick-image" href="javascript:void(0)">Add image</a></p>';
}
echo form_fieldset("French");
echo '<p>'.form_label("Title","title[fr]").'<br />'.form_input("title[fr]",$title["fr"]).'</p>';
echo '<p>'.form_label("Text","text[fr]").'<br />'.form_textarea("text[fr]",$text["fr"],'class="translation"').'</p>';
echo form_fieldset_close();
echo form_fieldset("English");
echo '<p>'.form_label("Title","title[en]").'<br />'.form_input("title[en]",$title["en"]).'</p>';
echo '<p>'.form_label("Text","text[en]").'<br />'.form_textarea("text[en]",$text["en"],'class="translation"').'</p>';
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
echo '<p>'.form_label("Space","space_ids[]").'<div id="spaces"></div></p>';
foreach ($space_ids as $space_id) {
    echo '<input type="hidden" name="space_ids[]" value="'.$space_id.'" />';
}
echo '<p>'.form_label("Artists","artist_ids[]").'<div id="artists"></div></p>';
foreach ($artist_ids as $artist_id) {
    echo '<input type="hidden" name="artist_ids[]" value="'.$artist_id.'" />';
}
echo form_hidden("image_id",$image_id);
echo '<p>'.form_submit("save","Save").'</p>';
echo form_close();
?>
<div id="imagePicker"></div>
<script src="//tinymce.cachefly.net/4.0/tinymce.min.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript">
    tinymce.init({
        selector: "textarea.translation",
        valid_elements: "a[href|target=_blank],strong/b,em/i,p",
        menubar: false,
        plugins: "link autolink",
        toolbar: "bold italic link unlink",
        statusbar: false
    });
    <?php
    echo 'var spaces = '.json_encode($js_spaces).';';
    ?>
    var imagePicker = new DivisionAdmin.ImagePicker();
    function addImage() {
        $('#imagePicker').show();
        imagePicker.load($('#imagePicker'),'/api/images',function(imageId){
            $('#imagePicker').empty().hide();
            if (imageId) {
                $("#image, #no-image").remove();
                $("input[name='image_id']").val(imageId);
                $("form").prepend('<div id="image"><p><a class="pick-image" href="javascript:void(0)"><img src="/images/440x235/'+imageId+'.jpg" /></a></p><p><a class="remove-image" href="javascript:void(0)">Remove image</a></p></div>');
                $('a.remove-image').on("click",removeImage);
                $('a.pick-image').on("click",addImage);
            }
        });
    }
    function removeImage() {
        $("#image, #no-image").remove();
        $("input[name='image_id']").val("");
        $("form").prepend($('<p id="no-image"></p>').append($('<a class="pick-image" href="javascript:void(0)">Add image</a>').on("click",addImage)));
    }
    $('a.remove-image').on("click",removeImage);
    $('a.pick-image').on("click",addImage);
    $("input.date").datepicker({"dateFormat":"yy-mm-dd"});

    var artistSelector = new DivisionAdmin.MultipleItemSelector(<?php echo json_encode($artist_options); ?>,<?php echo json_encode($artist_ids); ?>);
    artistSelector.changeCallback = function(selectedArtists){
        $("input[name='artist_ids[]']").remove();
        for (var i=0; i<selectedArtists.length; i++) {
            $("#artists").after('<input name="artist_ids[]" value="'+selectedArtists[i].id+'" type="hidden" />');
        }
    }
    artistSelector.appendTo($("#artists"));

    var spaceChangeCallback = function(selectedSpaces){
        $("input[name='space_ids[]']").remove();
        for (var i=0; i<selectedSpaces.length; i++) {
            $("#spaces").after('<input name="space_ids[]" value="'+selectedSpaces[i].id+'" type="hidden" />');
        }
    }

    var spaceSelector = new DivisionAdmin.MultipleItemSelector(<?php echo json_encode($space_options); ?>,<?php echo json_encode($space_ids); ?>);
    spaceSelector.changeCallback = spaceChangeCallback;
    spaceSelector.appendTo($("#spaces"));

    $("select[name='gallery_id']").on("change",function(){
        $("#spaces").empty();
        spaceSelector.remove();
        var gallery_id = $(this).val();
        var items = [];
        for (var i=0; i<spaces[gallery_id].length; i++) {
            items.push({"id":spaces[gallery_id][i].id,"text":spaces[gallery_id][i].name});
        }
        spaceSelector = new DivisionAdmin.MultipleItemSelector(items);
        spaceSelector.changeCallback = spaceChangeCallback;
        spaceSelector.appendTo($("#spaces"));
    });

    $("form").on("submit",function(){
        console.log($("form").get(0).elements);
    });

</script>