<?php
$lang = $this->config->item("language");
$title = $this->lang->line("Division Gallery");
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <title><?php echo $title; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no" />
    <link rel="stylesheet" href="/css/style.css" type="text/css" />
    <script type="text/javascript" src="/js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="/js/hammer/dist/jquery.hammer.js"></script>
    <script type="text/javascript" src="/js/main.js"></script>
    <script type="text/javascript">
        //<![CDATA[
        (function() {
            var images = <?php echo json_encode($images); ?>;
            var lang = "<?php echo $lang; ?>";
            var imageId = <?php echo $image_id; ?>;
            var dragging = false;
            function formatInches(number) {
                var fraction = (Math.round(number*4)/4) % 1;
                var num = Math.floor(number);
                switch (fraction) {
                    case 0.25:
                        return num+" ¼";
                    case 0.5:
                        return num+" ½";
                    case 0.75:
                        return num+" ¾";
                    default:
                        return num;
                }
            }
            function loadImage(id) {
                for (var i=0; i<images.length; i++) {
                    if (images[i].id == id) {
                        imageId = id;
                        trackView("<?php echo $gallery_id; ?>",imageId);
                        if ($("div.slide img[src='/images/2mp/"+id+(images[i].version ? "-"+images[i].version : "")+".jpg']").length > 0) {
                            $("div.slide img[src='/images/2mp/"+id+(images[i].version ? "-"+images[i].version : "")+".jpg']").parent().animate({"left":0,"right":0});
                        } else {
                            $("div.slide img").attr("src","/images/2mp/"+id+(images[i].version ? "-"+images[i].version : "")+".jpg");
                        }
                        $("div.slide, div.slide img").hammer().on("dragleft",onDragLeft).on("dragright",onDragRight).on("dragend",onDragEnd);
                        $("div.previous").toggleClass("disabled",i <= 0);
                        $("div.next").toggleClass("disabled", i >= images.length - 1);
                        if (i > 0) {
                            $("div.previous a").data("id",images[i-1].id).attr("href","<?php echo $base_url; ?>"+images[i-1].id);
                        } else {
                            $("div.previous a").attr("href","javascript:void(0)");
                        }
                        if (i < images.length - 1) {
                            $("div.next a").data("id",images[i+1].id).attr("href","<?php echo $base_url; ?>"+images[i+1].id);
                        } else {
                            $("div.next a").attr("href","javascript:void(0)");
                        }
                        $("div.imageInfo").empty();
                        var elements = [];
                        if (images[i].artists) {
                            var artistSpan = $('<span class="artists"></span>');
                            var artistLinks = [];
                            for (var artistId in images[i].artists) {
                                artistLinks.push($('<a href="/<?php echo $gallery_id; ?>/artist/'+artistId+'"></a>').text(images[i].artists[artistId]));
                            }
                            for (var j=0; j<artistLinks.length; j++) {
                                artistSpan.append(artistLinks[j]);
                                if (j<artistLinks.length-1) {
                                    artistSpan.append(document.createTextNode(", "));
                                }
                            }
                            elements.push(artistSpan);
                        }
                        if (images[i].hasOwnProperty("title")) {
                            var titleSpan = $('<span class="title"></span>');
                            if (images[i].title.hasOwnProperty(lang)) {
                                titleSpan.text(images[i].title[lang]);
                            } else {
                                for (var lng in images[i].title) {
                                    titleSpan.text(images[i].title[lng]);
                                    break;
                                }
                            }
                            if (elements.length > 0) {
                                artistSpan.append(document.createTextNode(": ")).append(titleSpan);
                            } else {
                                elements.push(titleSpan);
                            }
                        }
                        if (images[i].hasOwnProperty("year")) {
                            elements.push($('<span class="year"></span>').text(images[i].year));
                        }
                        var dimensions = [];
                        if (images[i].hasOwnProperty("height") && images[i].height) {
                            dimensions.push(formatInches(images[i].height)+'"');
                        }
                        if (images[i].hasOwnProperty("width") && images[i].width) {
                            dimensions.push(formatInches(images[i].width)+'"');
                        }
                        if (images[i].hasOwnProperty("depth") && images[i].depth) {
                            dimensions.push(formatInches(images[i].depth)+'"');
                        }
                        if (dimensions.length > 0) {
                            dimensions = dimensions.join(" × ");
                            elements.push($('<span class="dimensions"></span>').text(dimensions));
                        }
                        if (images[i].hasOwnProperty("description")) {
                            if (images[i].description.hasOwnProperty(lang)) {
                                elements.push($('<span class="description"></span>').text(images[i].description[lang]));
                            } else {
                                for (var lng in images[i].description) {
                                    elements.push($('<span class="description" lang="'+lng+'"></span>').text(images[i].description[lng]));
                                    break;
                                }
                            }
                        }
                        for (var j=0; j<elements.length; j++) {
                            $("div.imageInfo").append(elements[j]);
                            if (j < elements.length - 1) {
                                $("div.imageInfo").append(document.createTextNode(", "));
                            }
                        }
                        break;
                    }
                }
            }
            function onDragLeft(event) {
                event.stopPropagation();
                event.preventDefault();
                var nextSlide = null;
                for (var i=0; i<images.length; i++) {
                    if (images[i].id == imageId) {
                        if (i >= images.length - 1) {
                            dragging = false;
                            return;
                        }
                        nextSlide = images[i+1];
                        break;
                    }
                }
                dragging = true;
                var currentSlide = $("div.slide");
                var tempSlide = $("div.tempSlide");
                if (tempSlide.length == 0) {
                    tempSlide = currentSlide.clone();
                    tempSlide.removeClass("slide").addClass("tempSlide");
                    tempSlide.find("img").attr("src","/images/2mp/"+nextSlide.id+(nextSlide.version ? "-"+nextSlide.version : "")+".jpg");
                    tempSlide.attr("name",nextSlide.id);
                    tempSlide.insertAfter(currentSlide);
                }
                var width = currentSlide.width();
                console.log("left="+(width+event.gesture.deltaX),"right="+(0-(width+event.gesture.deltaX)));
                tempSlide.css({"left":width+event.gesture.deltaX,"right":0-(width+event.gesture.deltaX)});
                currentSlide.css({"left":event.gesture.deltaX,"right":0-event.gesture.deltaX});
            }
            function onDragRight(event) {
                event.stopPropagation();
                event.preventDefault();
                var nextSlide = null;
                for (var i=0; i<images.length; i++) {
                    if (images[i].id == imageId) {
                        if (i <= 0) {
                            dragging = false;
                            return;
                        }
                        nextSlide = images[i-1];
                        break;
                    }
                }
                dragging = true;
                var currentSlide = $("div.slide");
                var tempSlide = $("div.tempSlide");
                if (tempSlide.length == 0) {
                    tempSlide = currentSlide.clone();
                    tempSlide.removeClass("slide").addClass("tempSlide");
                    tempSlide.find("img").attr("src","/images/2mp/"+nextSlide.id+(nextSlide.version ? "-"+nextSlide.version : "")+".jpg");
                    tempSlide.attr("name",nextSlide.id);
                    tempSlide.insertAfter(currentSlide);
                }
                var width = currentSlide.width();
                tempSlide.css({"left":event.gesture.deltaX-width,"right":0-(event.gesture.deltaX-width)});
                currentSlide.css({"left":event.gesture.deltaX,"right":0-event.gesture.deltaX});
            }
            function onDragEnd(event){
                if (dragging) {
                    $("div.slide").remove();
                    var id = $("div.tempSlide").attr("name");
                    $("div.tempSlide").removeClass("tempSlide").addClass("slide");
                    history.pushState({"id":id},"","<?php echo $base_url; ?>"+id);
                    loadImage(id);
                    dragging = false;
                }
            }
            $(document).on("ready",function(){
                $(window).on("popstate",function(event){
                    if (event.originalEvent.state) {
                        loadImage(event.originalEvent.state.id);
                    }
                });
                $("div.previous, div.next").on("click","a",function(){
                    if ($(this).parent().hasClass("disabled")) {
                        return false;
                    }
                    var id = $(this).data("id");
                    history.pushState({"id":id},"",$(this).attr("href"));
                    loadImage(id);
                    return false;
                });
                $("div.slide, div.slide img").hammer().on("dragleft",onDragLeft).on("dragright",onDragRight).on("dragend",onDragEnd);
                trackView("<?php echo $gallery_id; ?>",imageId);
            });
        })();
        //]]>
    </script>
</head>
<body>
<div class="imageViewer">
<?php
function format_inches($num) {
    $number = floor($num);
    $fraction = (round($num*4)/4) - $number;
    switch ($fraction) {
        case 0.25:
            return $number." &frac14;";
        case 0.5:
            return $number." &frac12;";
        case 0.75:
            return $number." &frac34;";
        default:
            return $number;
    }
}
if (!empty($images)) {
    if (empty($image_id)) {
        $image_id = $images[0]["id"];
    }
    for ($i=0; $i<count($images); $i++) {
        if ($images[$i]["id"] == $image_id) {
            $image = $images[$i];
            $artists = array();
            $info = array();
            if (!empty($image["artists"])) {
                foreach ($image["artists"] as $id=>$image_artist) {
                    $artists[] = '<a class="artist" href="/'.$gallery_id.'/artist/'.$id.'">'.htmlspecialchars($image_artist).'</a>';
                }
                $info[] = join(", ",$artists);
            }
            $titleSpan = null;
            if (!empty($image["title"][$lang])) {
                $titleSpan = '<span class="title">'.htmlspecialchars($image["title"][$lang]).'</span>';
            } else if (!empty($image["title"])) {
                foreach ($image["title"] as $language=>$title) {
                    if (!empty($title)) {
                        $titleSpan = '<span class="title" lang="'.$language.'">'.htmlspecialchars($title).'</span>';
                        break;
                    }
                }
            }
            if ($titleSpan) {
                if (count($info) > 0) {
                    $info[0] .= " ".$titleSpan;
                } else {
                    $info[] = $titleSpan;
                }
            }
            if (!empty($image["year"])) {
                $info[] = '<span class="year">'.htmlspecialchars($image["year"]).'</span>';
            }
            $dimensions = array();
            if (!empty($image["height"])) {
                $dimensions[] = format_inches($image["height"]).'&quot;';
            }
            if (!empty($image["width"])) {
                $dimensions[] = format_inches($image["width"]).'&quot;';
            }
            if (!empty($image["depth"])) {
                $dimensions[] = format_inches($image["depth"]).'&quot;';
            }
            if (!empty($dimensions)) {
                $info[] = '<span class="dimensions">'.join(" &times; ",$dimensions).'</span>';
            }
            if (!empty($image["description"][$lang])) {
                $info[] = '<span class="description">'.htmlspecialchars($image["description"][$lang]).'</span>';
            } else if (!empty($image["description"])) {
                foreach ($image["description"] as $language=>$description) {
                    if (!empty($description)) {
                        $info[] = '<span class="description" lang="'.$language.'">'.htmlspecialchars($description).'</span>';
                        break;
                    }
                }
            }
            if ($i > 0) {
                echo '<div class="previous"><a data-id="'.$images[$i-1]["id"].'" href="'.$base_url.$images[$i-1]["id"].'">&lt;</a></div>';
            } else {
                echo '<div class="previous disabled"><a data-id="'.$images[$i]["id"].'" href="javascript:void(0)">&lt;</a></div>';
            }
            echo '<div class="slide"><img src="/images/2mp/'.$image["id"].($image["version"] ? "-".$image["version"] : "").'.jpg" /></div>';
            if ($i < count($images) - 1) {
                echo '<div class="next"><a data-id="'.$images[$i+1]["id"].'" href="'.$base_url.$images[$i+1]["id"].'">&gt;</a></div>';
            } else {
                echo '<div class="next disabled"><a data-id="'.$images[$i]["id"].'" href="javascript:void(0)">&gt;</a></div>';
            }
            echo '<div class="imageInfo">'.join(", ",$info).'</div>';
            echo '<div class="close"><a href="'.$parent_url.'">&times;</a></div>';
            break;
        }
    }
}
?>
</div>
</body>
</html>