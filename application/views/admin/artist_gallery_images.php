<?php
echo '<h1>'.$artist_name.'</h1>';
echo '<div id="gallery_images"><h3>Images shown on the '.$gallery_name.' website</h3>';
$this->load->view("admin/image_list",array("images"=>$gallery_images));
echo '</div>';
echo '<div id="all_images"><h3>All available images</h3>';
$this->load->view("admin/image_list",array("images"=>$all_images));
echo '</div>';
?>
<p><button id="saveButton" style="display:none">Save</button></p>
<script type="text/javascript" src="/js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript">
    function onUpdate() {
        $("#saveButton").show().on("click",save);
    }
    function save() {
        $("#saveButton").off("click").hide();
        var data = {"images":[]};
        $("#gallery_images ul.thumbnails a.thumbnail").each(function(){
            data.images.push($(this).attr("data-id"));
        });
        function onError() {
            alert("Error saving images.");
            location.reload();
        }
        $.ajax({
            "url":"/api/artist/<?php echo $artist_id; ?>/images/<?php echo $gallery_id; ?>",
            "type":"post",
            "dataType":"json",
            "success":function(data){
                if (!data) {
                    onError();
                }
            },
            "error":onError,
            "data":data
        });
    }
    $(document).on("ready",function(){
        $("#gallery_images ul.thumbnails").sortable();
        $("#gallery_images ul.thumbnails").disableSelection();
        $("#gallery_images ul.thumbnails").on("sortstop",onUpdate);
    });
    function onRemoveImage() {
        $(this).parent().remove();
        onUpdate();
        return false;
    }
    $("#gallery_images ul.thumbnails a.thumbnail").on("click",onRemoveImage);
    $("#all_images ul.thumbnails a.thumbnail").on("click",function(){
        var imageId = $(this).attr("data-id");
        var galleryList = $("#gallery_images ul.thumbnails");
        if (galleryList.length == 0) {
            galleryList = $('<ul class="thumbnails"></ul>').appendTo("#gallery_images");
            galleryList.sortable();
            galleryList.disableSelection();
            galleryList.on("sortstop",onUpdate);
        }
        if (galleryList.find("a.thumbnail[data-id='"+imageId+"']").length == 0) {
            var clone = $(this).clone().on("click",onRemoveImage);
            galleryList.prepend($('<li></li>').append(clone));
            onUpdate();
        }
        return false;
    });
</script>