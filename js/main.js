(function(){
    $(document).on("ready",function(){
        $("#menuButton").on("click",function(){
            $("header nav").toggle();
        });
        function loadFeatureImage(container,src) {
            var oldImg = container.find("img.feature");
            var newImg = $('<img class="feature" alt="image" src="'+src+'" style="left:440px" />');
            container.append(newImg);
            oldImg.animate({"left":-440},{"duration":500});
            newImg.animate({"left":0},{"duration":500,"complete":function(){
                oldImg.remove();
            }});
        }
        var rotateIntervals = [];
        $("a.feature").each(function(){
            if ($(this).data("image_ids") && $(this).data("image_ids").length  > 1) {
                var currentIndex = 0;
                var images = [];
                for (var i=0; i<$(this).data("image_ids").length; i++) {
                    images.push("/images/440x235/"+$(this).data("image_ids")[i]+".jpg");
                }
                var link = $(this);
                rotateIntervals.push(setInterval(function(){
                    currentIndex ++;
                    if (currentIndex >= images.length) {
                        currentIndex = 0;
                    }
                    loadFeatureImage(link,images[currentIndex]);
                },3000));
            }
        });
        if ('localStorage' in window && window.localStorage !== null && location.hash.length > 1) {
            var varPairs = location.hash.substring(1).split("&");
            var name, email;
            for (var i=0; i<varPairs.length; i++) {
                var val = varPairs[i].split("=");
                if (val[0] == "email" && val.length == 2) {
                    email = val[1];

                } else if (val[0] == "name" && val.length == 2) {
                    name = decodeURIComponent(val[1]);
                }
            }
            if (email) {
                saveContact(email,name);
            }
        }
    });
})();
function trackView(galleryId,workId) {
    if ('localStorage' in window && window.localStorage !== null) {
        var email = localStorage.getItem("email");
        var name = localStorage.getItem("name");
        if (email) {
            $.ajax({
                "url":"/track",
                "type":"post",
                "dataType":"json",
                "data":{
                    "email":email,
                    "name":name,
                    "gallery_id":galleryId,
                    "work_id":workId
                }
            });
        }
    }
}
function saveContact(email,name) {
    if ('localStorage' in window && window.localStorage !== null) {
        localStorage.setItem("email",email);
        localStorage.setItem("name",name);
    }
}