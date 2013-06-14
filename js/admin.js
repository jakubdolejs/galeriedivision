DivisionAdmin = {
    imageUpload: function() {
        var xhr = null;
        this.subscribe = function(eventType,callback) {
            if (!callbacks.hasOwnProperty(eventType)) {
                return;
            }
            callbacks[eventType].add(callback);
        }
        this.unsubscribe = function(callback) {
            if (!callbacks.hasOwnProperty(eventType)) {
                return;
            }
            callbacks[eventType].remove(callback);
        }
        var callbacks = {
            progress: $.Callbacks(),
            complete: $.Callbacks(),
            error: $.Callbacks(),
            abort: $.Callbacks()
        };
        this.upload = function(files) {
            xhr = new XMLHttpRequest();
            var fd = new FormData();
            for (var i=0; i<files.length; i++) {
                fd.append("file["+i+"]", files[i]);
            }
            xhr.upload.addEventListener("progress", function(event) {
                if (event.lengthComputable) {
                    callbacks.progress.fire({"loaded":event.loaded,"total":event.total});
                }
            }, false);
            xhr.addEventListener("load", function(event) {
                var response = JSON.parse(event.target.responseText);
                if (!response.hasOwnProperty("error")) {
                    callbacks.complete.fire(response);
                } else {
                    callbacks.error.fire(response.error);
                }
            }, false);
            xhr.addEventListener("error", function(event) {
                callbacks.error.fire(event);
            }, false);
            xhr.addEventListener("abort", function(event) {
                callbacks.abort.fire(event);
            }, false);
            xhr.open("POST", "/admin/image_upload");
            xhr.send(fd);
        }
        this.abort = function() {
            if (xhr) {
                xhr.abort();
            }
        }
    },
    imageForm: function(imageId) {
        var _this = this;
        var form = $('<form id="imageForm'+imageId+'" method="post"></form>');
        var loader = $('<p>Loading image info</p>').appendTo(form);
        $.ajax({
            "url":"/admin/image/"+imageId,
            "dataType":"json",
            "success":function(data) {
                loader.remove();
                $('#imageForm'+imageId).remove();
                form.append('<p><img src="/images/185/'+imageId+'.jpg" alt="image" /></p>');
                form.append($('<p><label for="width">Width</label><br /></p>').append('<input type="text" name="width" />'));
                if (data.width) {
                    form.find("input[name='width']").val(data.width);
                }
                form.append($('<p><label for="height">Height</label><br /></p>').append('<input type="text" name="height" />'));
                if (data.height) {
                    form.find("input[name='height']").val(data.height);
                }
                form.append($('<p><label for="depth">Depth</label><br /></p>').append('<input type="text" name="depth" />'));
                if (data.depth) {
                    form.find("input[name='depth']").val(data.depth);
                }
                form.append($('<p><label for="creation_year">Year</label><br /></p>').append('<input type="text" name="creation_year" />'));
                if (data.creation_year) {
                    form.find("input[name='creation_year']").val(data.creation_year);
                }
            },
            "error":function() {
                loader.text("Error loading image info");
            }
        });
        return form;
    },
    ImagePicker: function() {
        this.load = function(container,src,callback) {
            $.ajax({
                "url":src,
                "dataType":"json",
                "success":function(data) {
                    $(container).empty().append($('<p></p>').append($('<a href="javascript:void(0)">Cancel</a>').on("click",function(){
                        callback(null);
                    })));
                    if (data.length > 0) {
                        for (var i=0; i<data.length; i++) {
                            container.append($('<a href="javascript:void(0)" data-id="'+data[i].id+'" class="image-picker"><img src="/images/185/'+data[i].id+'.jpg" /></a>').on("click",function(){
                                callback($(this).attr("data-id"));
                            }));
                        }
                    }
                },
                "error":function() {
                    callback(null);
                }
            });
        }
    }
}