<div id="upload"></div>
<div id="imageList">
<?php
if (!empty($images)) {
    $this->load->view("admin/image_list",array("images"=>$images));
}
?>
</div>
<script type="text/javascript" src="/js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
<script type="text/javascript" src="/js/jquery.exif.js"></script>
<script type="text/javascript">
    //<![CDATA[
    $("#upload").imageUpload().on("complete",function(){
        location.reload();
    });
    //]]>
</script>
