<?php
if (!empty($news)) {
    $action = "/admin/news/".$news["id"];
    $date = DateTime::createFromFormat("Y-m-d",$news["date_published"]);
} else {
    $action = "/admin/news";
    $date = new DateTime();
}
$gallery_options = array();
foreach ($galleries as $gallery) {
    $gallery_options[] = array("id"=>$gallery["id"],"text"=>$gallery["city"]);
}
$artist_options = array();
foreach ($artists as $artist) {
    $artist_options[] = array("id"=>$artist["id"],"text"=>$artist["name"]);
}
$exhibition_options = array();
foreach ($exhibitions as $exhibition) {
    $exhibition_options[] = array("id"=>$exhibition["id"],"text"=>$exhibition["title"]);
}
$this->load->helper("form");
echo form_open_multipart($action,'method="post"');
if (!empty($news["image_id"])) {
    echo '<div id="image"><p><a class="pick-image" href="javascript:void(0)"><img src="/images/440x235/'.$news["image_id"].'.jpg" /></a></p><p><a class="remove-image" href="javascript:void(0)">Remove image</a></p></div>';
} else {
    echo '<p id="no-image"><a class="pick-image" href="javascript:void(0)">Add image</a></p>';
}
echo form_fieldset("French");
echo '<p>'.form_label("Headline","headline[fr]").'<br />'.form_input("headline[fr]",@$news["headline"]["fr"]).'</p>';
echo '<p>'.form_label("Text","text[fr]").'<br />'.form_textarea("text[fr]",@$news["text"]["fr"],'class="translation"').'</p>';
echo form_fieldset_close();
echo form_fieldset("English");
echo '<p>'.form_label("Headline","headline[en]").'<br />'.form_input("headline[en]",@$news["headline"]["en"]).'</p>';
echo '<p>'.form_label("Text","text[en]").'<br />'.form_textarea("text[en]",@$news["text"]["en"],'class="translation"').'</p>';
echo form_fieldset_close();
echo '<p>'.form_label("Source (e.g. Arforum Summer 2013)","source").'<br />'.form_input("source",@$news["source"]).'</p>';
echo '<p>'.form_label("Publish date","date").'<br />'.form_input("date",$date->format("Y-m-d"),'class="date"').'</p>';
echo '<p>'.form_label("Link to original article","url").'<br /><input type="url" placeholder="http://" name="url" value="'.@$news["url"].'" /></p>';
echo '<p>'.form_label("Galleries on whose websites this story will appear","gallery_ids[]").'<div id="galleries"></div></p>';
if (!empty($news["galleries"])) {
    foreach ($news["galleries"] as $gallery_id) {
        echo '<input type="hidden" name="gallery_ids[]" value="'.$gallery_id.'" />';
    }
}
echo '<p>'.form_label("Artists mentioned in the story","artist_ids[]").'<div id="artists"></div></p>';
if (!empty($news["artists"])) {
    foreach ($news["artists"] as $artist_id=>$name) {
        echo '<input type="hidden" name="artist_ids[]" value="'.$artist_id.'" />';
    }
}
echo '<p>'.form_label("Exhibitions mentioned in the story","exhibition_ids[]").'<div id="exhibitions"></div></p>';
if (!empty($news["exhibitions"])) {
    foreach ($news["exhibitions"] as $exhibition_id=>$name) {
        echo '<input type="hidden" name="exhibition_ids[]" value="'.$exhibition_id.'" />';
    }
}
if (!empty($news["image_id"])) {
    echo form_hidden("image_id",$news["image_id"]);
}
echo '<p>'.form_label("PDF file","pdf").'<br /><input type="file" name="pdf" accept="*.pdf" /></p>';
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
    var imagePicker = new DivisionAdmin.ImagePicker();
    function addImage() {
        $('#imagePicker').show();
        imagePicker.load($('#imagePicker'),'/api/images',function(imageId){
            $('#imagePicker').empty().hide();
            if (imageId) {
                $("input[name='image_id'], #image, #no-image").remove();
                $("form").append('<input name="image_id" type="hidden" value="'+imageId+'" />');
                $("form").prepend('<div id="image"><p><a class="pick-image" href="javascript:void(0)"><img src="/images/440x235/'+imageId+'.jpg" /></a></p><p><a class="remove-image" href="javascript:void(0)">Remove image</a></p></div>');
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

    var artistSelector = new DivisionAdmin.MultipleItemSelector(<?php echo json_encode($artist_options); ?>,<?php echo !empty($news["artists"]) ? json_encode(array_keys($news["artists"])) : "[]"; ?>);
    artistSelector.changeCallback = function(selectedArtists){
        $("input[name='artist_ids[]']").remove();
        for (var i=0; i<selectedArtists.length; i++) {
            $("#artists").after('<input name="artist_ids[]" value="'+selectedArtists[i].id+'" type="hidden" />');
        }
    }
    artistSelector.appendTo($("#artists"));

    var gallerySelector = new DivisionAdmin.MultipleItemSelector(<?php echo json_encode($gallery_options); ?>,<?php echo !empty($news["galleries"]) ? json_encode($news["galleries"]) : "[]"; ?>);
    gallerySelector.changeCallback = function(selectedGalleries){
        $("input[name='gallery_ids[]']").remove();
        for (var i=0; i<selectedGalleries.length; i++) {
            $("#galleries").after('<input name="gallery_ids[]" value="'+selectedGalleries[i].id+'" type="hidden" />');
        }
    }
    gallerySelector.appendTo($("#galleries"));

    var exhibitionSelector = new DivisionAdmin.MultipleItemSelector(<?php echo json_encode($exhibition_options); ?>,<?php echo !empty($news["exhibitions"]) ? json_encode(array_keys($news["exhibitions"])) : "[]"; ?>);
    exhibitionSelector.changeCallback = function(selectedExhibitions){
        $("input[name='exhibition_ids[]']").remove();
        for (var i=0; i<selectedExhibitions.length; i++) {
            $("#exhibitions").after('<input name="exhibition_ids[]" value="'+selectedExhibitions[i].id+'" type="hidden" />');
        }
    }
    exhibitionSelector.appendTo($("#exhibitions"));

    $("form").on("submit",function(){
        console.log($("form").get(0).elements);
    });

</script>
