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
                    images.push("/images/w440/"+$(this).data("image_ids")[i]+".jpg");
                }
                var link = $(this);
                rotateIntervals.push(setInterval(function(){
                    currentIndex ++;
                    if (currentIndex >= images.length) {
                        currentIndex = 0;
                    }
                    loadFeatureImage(link,images[currentIndex]);
                },5000));
            }
        });

    });
})();