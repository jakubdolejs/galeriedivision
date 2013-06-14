<?php
if (!empty($artist)) {
    $this->load->helper("form");
    echo form_open("/admin/artist/".$artist["id"],array("method"=>"post"));
    echo '<p>'.form_label("Name","name").'<br />'.form_input("name",$artist["name"]).'</p>';
    foreach ($artist["galleries"] as $id=>$gallery) {
        if (!$user["superuser"] && !in_array($id,$user["galleries"])) {
           continue;
        }
        echo '<h3>'.$gallery["city"].'</h3><div id="gallery'.$id.'">';
        if (!empty($gallery["image_id"])) {
            echo '<p><a class="pick-image" data-gallery_id="'.$id.'" href="javascript:void(0)"><img src="/images/185/'.$gallery["image_id"].'.jpg" style="max-width:92px; max-height:92px" /></a></p>';
            echo '<p><a class="remove-image" data-gallery_id="'.$id.'" href="javascript:void(0)">Remove image</a></p>';
            echo form_hidden('image_id['.$id.']',$gallery["image_id"]);
        } else {
            echo '<p><a class="add-image" data-gallery_id="'.$id.'" href="javascript:void(0)">Add image</a></p>';
        }
        echo '</div><p>'.form_checkbox("available[".$id."]",1,$gallery["available"]).form_label("Listed","available[".$id."]").'<br />
        '.form_checkbox("represented[".$id."]",1,$gallery["represented"]).form_label("Represented","represented[".$id."]").'</p>
        <p><a href="/admin/artist/'.$artist["id"].'/images/'.$id.'">Images</a></p>';
    }
    echo '<p>'.form_submit("save","Save").'</p>';
    echo form_close();
?>
<div id="imagePicker"></div>
<script type="text/javascript">
    var imagePicker = new DivisionAdmin.ImagePicker();
    $('a.pick-image, a.add-image').on("click",addImage);
    function addImage() {
        var link = $(this);
        var galleryId = link.attr("data-gallery_id");
        $('#imagePicker').show();
        imagePicker.load($('#imagePicker'),'/api/artist/<?php echo $artist["id"]; ?>/images',function(imageId){
            $('#imagePicker').empty().hide();
            if (imageId) {
                var galleryDiv = $('#gallery'+galleryId);
                galleryDiv.empty();
                galleryDiv.append($('<p></p>').append($('<a class="pick-image" data-gallery_id="'+galleryId+'" href="javascript:void(0)"><img src="/images/185/'+imageId+'.jpg" style="max-width:92px; max-height:92px" /></a>').on("click",addImage)));
                galleryDiv.append($('<p></p>').append($('<a class="remove-image" data-gallery_id="'+galleryId+'" href="javascript:void(0)">Remove image</a>').on("click",removeImage)));
                $('<input type="hidden" name="image_id['+galleryId+']" value="'+imageId+'" />').appendTo(galleryDiv);
            }
        });
    }
    function removeImage() {
        var link = $(this);
        var galleryId = link.attr("data-gallery_id");
        var galleryDiv = $('#gallery'+galleryId);
        galleryDiv.empty().append($('<p></p>').append($('<a class="add-image" data-gallery_id="'+galleryId+'" href="javascript:void(0);">Add image</a>').on("click",addImage)));
    }
    $('a.remove-image').on("click",removeImage);
</script>
<?php
}
?>