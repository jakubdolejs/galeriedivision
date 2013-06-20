<form id="uploadForm" method="post" enctype="multipart/form-data">
<p><input type="file" name="file" accept="image/*" /></p>
<p><button id="cancelBtn" type="reset" style="display:none">Cancel</button> <button id="uploadBtn" disabled="disabled">Upload</button></p>
</form>
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
    var crop;
    $("#uploadBtn").click(function(){
        var files = $("input[name='file']").get(0).files;
        var maxFiles = 1;
        if (files.length > maxFiles) {
            alert("Please upload up to "+maxFiles+" files at a time.");
            return false;
        }
        if (files.length > 0) {
            $("#uploadForm").hide();
            $('.imagePreview p').eq(0).remove();
            $("#content").append('<p id="uploading">Uploading. Please wait.</p>');

            function onProgress(data) {
                if ($("#progressBarContainer").length == 0) {
                    $('<div id="progressBarContainer"><div id="progressBar"></div></div>').appendTo($("#content"));
                }
                $("#progressBar").width(data.loaded/data.total*$("#progressBarContainer").width());
            }
            function onComplete(response) {
                $("#progressBarContainer, #uploading").remove();
                for (var i=0; i<response.length; i++) {
                    if (response[i].hasOwnProperty("id")) {
                        location.href = "/admin/image/"+response[i].id;
                        return;
                    }
                }
            }
            function onError(error) {
                $("#progressBarContainer, #uploading").remove();
                $("#uploadForm").show();
                alert("Error uploading file");
            }

            var imageUpload = new DivisionAdmin.imageUpload();
            imageUpload.subscribe("progress",onProgress);
            imageUpload.subscribe("complete",onComplete);
            imageUpload.subscribe("error",onError);
            imageUpload.subscribe("abort",onError);
            imageUpload.upload(files,[crop]);
        }
        return false;
    });
    $("#cancelBtn").on("click",function(){
        $('.imagePreview').remove();
        $("#uploadBtn").attr("disabled","disabled");
        $("input[name='file'], #imageList").show();
        $(this).hide();
    });
    $("input[name='file']").on("change",function(){
        $("input[name='file'], #imageList").hide();
        $("#cancelBtn").show();
        if (this.files.length > 0) {
            var files = this.files;
            try {
                $(this).fileExif(function(exif){
                    console.log(exif);
                    if (window.File && window.FileReader && window.FileList && window.Blob) {
                        for (var i=0, f; f=files[i]; i++) {
                            if (!f.type.match('image.*')) {
                                continue;
                            }
                            var reader = new FileReader();
                            reader.onload = (function(theFile) {
                                return function(e) {
                                    $('.imagePreview').remove();
                                    var img = $('<img style="position:absolute" />');
                                    img.on("load",function(){
                                        $(this).off("load");
                                        var originalWidth = $(this).get(0).naturalWidth;
                                        var originalHeight = $(this).get(0).naturalHeight;
                                        var cropWidth = 440;
                                        var cropHeight = 235;
                                        var rotation = 0;
                                        if (exif && exif.hasOwnProperty("Orientation")) {
                                            switch (exif.Orientation) {
                                                case 2:
                                                    //horizontal flip
                                                    break;
                                                case 3:
                                                    rotation = 180;
                                                    break;
                                                case 4:
                                                    //vertical flip
                                                    break;
                                                case 5:
                                                    //vertical flip
                                                    var ow = originalWidth;
                                                    originalWidth = originalHeight;
                                                    originalHeight = ow;
                                                    rotation = -90;
                                                    break;
                                                case 6:
                                                    var ow = originalWidth;
                                                    originalWidth = originalHeight;
                                                    originalHeight = ow;
                                                    rotation = 90;
                                                    break;
                                                case 7:
                                                    //horizontal flip
                                                    var ow = originalWidth;
                                                    originalWidth = originalHeight;
                                                    originalHeight = ow;
                                                    rotation = -90;
                                                    break;
                                                case 8:
                                                    var ow = originalWidth;
                                                    originalWidth = originalHeight;
                                                    originalHeight = ow;
                                                    rotation = -90;
                                                    break;
                                            }
                                        }
                                        if (originalWidth < 900) {
                                            $('.imagePreview').remove();
                                            $('#uploadForm').prepend("<p>The image is too small. Please make sure it's at least 900 pixels wide.</p>");
                                        } else {
                                            var imageRatio = originalWidth/originalHeight;
                                            var cropRatio = cropWidth/cropHeight;
                                            var w, h, axis;
                                            if (imageRatio < cropRatio) {
                                                w = cropWidth;
                                                h = Math.round(w/imageRatio);
                                                axis = "y";
                                            } else {
                                                h = cropHeight;
                                                w = Math.round(h*imageRatio);
                                                axis = "x";
                                            }

                                            canvas.attr("width",w).attr("height",h);
                                            var ctx = canvas.get(0).getContext("2d");

                                            if (rotation == 90) {
                                                ctx.rotate(rotation*Math.PI/180);
                                                ctx.drawImage(img.get(0),0,0-w,h,w);
                                            } else if (rotation == -90) {
                                                ctx.rotate(rotation*Math.PI/180);
                                                ctx.drawImage(img.get(0),0-h,0,h,w);
                                            } else {
                                                ctx.drawImage(img.get(0),0,0,w,h);
                                            }

                                            var scale = w/originalWidth;
                                            var imgContainerSize = {"width":(w-cropWidth)+w,"height":(h-cropHeight)+h,"left":cropWidth/2-((w-cropWidth)+w)/2,"top":cropHeight/2-((h-cropHeight)+h)/2};
                                            canvas.draggable({"axis":axis,"containment":"parent"}).on("dragstop",function(event,ui){
                                                $("input[name='crop[440x235]']").val(ui.position.left+','+ui.position.top);
                                                crop = [Math.round((w-cropWidth-ui.position.left)/scale),Math.round((h-cropHeight-ui.position.top)/scale),Math.round(cropWidth/scale),Math.round(cropHeight/scale)];
                                            });
                                            $("#croppedImage div").css(imgContainerSize);
                                            $("#croppedImage").css({"overflow":"hidden"});
                                            crop = [Math.round((w-cropWidth-canvas.position().left)/scale),Math.round((h-cropHeight-canvas.position().top)/scale),Math.round(cropWidth/scale),Math.round(cropHeight/scale)];
                                            $('<input type="hidden" name="crop[440x235]" value="'+canvas.position().left+','+canvas.position().top+'" />').appendTo($("body"));
                                            $('<input type="hidden" name="scale[440x235]" value="'+scale+'" />').appendTo($("body"));
                                            $("#uploadBtn").removeAttr("disabled");
                                        }
                                    });
                                    $('.imagePreview').remove();
                                    $("#uploadForm").before('<p class="imagePreview"></p>');
                                    $("p.imagePreview").append('<p>Adjust image cropping for exhibition pages</p>').append('<div style="width:440px;height:235px;position:relative;oveflow:hidden" id="croppedImage"><div style="position:absolute"></div></div>');
                                    var canvas = $('<canvas></canvas>');
                                    $("#croppedImage div").append(canvas);
                                    img.attr("src",e.target.result);
                                };
                            })(f);
                            reader.readAsDataURL(f);
                        }
                    } else {
                        alert("Your browser does not support advanced features that allow for cropping images before they are uploaded. Please use the latest version of Google Chrome.");
                    }
                });
            } catch (error) {
                alert("Your browser does not support advanced features that allow for cropping images before they are uploaded. Please use the latest version of Google Chrome.");
            }
        } else {
            $("#uploadBtn").attr("disabled","disabled");
        }
    });
    //]]>
</script>
