(function(){
    $(document).on("ready",function(){
        $("#menuButton").on("click",function(){
            $("header nav ul").toggle();
        });
    });
})();