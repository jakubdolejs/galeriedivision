<form id="uploadForm" method="post" enctype="multipart/form-data">
<p><input type="file" name="file" accept="image/*" /></p>
<p><button id="uploadBtn" disabled="disabled">Upload</button></p>
</form>
<?php
if (!empty($images)) {
    $this->load->view("admin/image_list",array("images"=>$images));
}
?>
<script type="text/javascript">
    //<![CDATA[
    $("input[name='file']").on("change",function(){
        if (this.files.length > 0) {
            $("#uploadBtn").removeAttr("disabled");
            if (window.File && window.FileReader && window.FileList && window.Blob) {
                for (var i=0, f; f=this.files[i]; i++) {
                    if (!f.type.match('image.*')) {
                        continue;
                    }
                    var reader = new FileReader();
                    reader.onload = (function(theFile) {
                        return function(e) {
                            $('#imagePreview').remove();
                            $('#uploadForm').prepend(['<p id="imagePreview"><img style="max-width: 200px; max-height: 200px" src="', e.target.result,
                                '" title="', escape(theFile.name), '"/></p>'].join(''));
                        };
                    })(f);
                    reader.readAsDataURL(f);
                }
            }
        } else {
            $("#uploadBtn").attr("disabled","disabled");
        }
    });
    $("#uploadBtn").click(function(){
        var files = $("input[name='file']").get(0).files;
        var maxFiles = 5;
        if (files.length > maxFiles) {
            alert("Please upload up to "+maxFiles+" files at a time.");
            return false;
        }
        if (files.length > 0) {
            $("#uploadForm").hide();
            $("body").append('<p id="uploading">Uploading. Please wait.</p>');

            function onProgress(data) {
                if ($("#progressBarContainer").length == 0) {
                    $('<div id="progressBarContainer"><div id="progressBar"></div></div>').appendTo($("body"));
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
            imageUpload.upload(files);
        }
        return false;
    });
    //]]>
</script>
