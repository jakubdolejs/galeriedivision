<h1><?php echo $this->lang->line("News"); ?></h1>
<?php
echo '<div class="subscribe">';
$this->load->view("subscribe");
echo '</div>';
echo '<ul class="news">';
if (!empty($news)) {
    foreach ($news as $story) {
        echo '<li class="story"><h2>'.$story["headline"].'</h2>';
        if (!empty($story["source"])) {
            echo '<h3>'.htmlspecialchars($story["source"]).'</h3>';
        }
        if (!empty($story["image_id"]) && !empty($story["text"])) {
            echo '<div class="image text" data-id="'.$story["id"].'"><img src="/images/185/'.$story["image_id"].'.jpg" alt="image" /><div>'.$story["text"].'</div></div>';
        } else if (!empty($story["text"])) {
            echo '<div class="text" data-id="'.$story["id"].'"><div>'.$story["text"].'</div></div>';
        } else if (!empty($story["image_id"])) {
            echo '<div class="image" data-id="'.$story["id"].'"><img src="/images/440x235/'.$story["image_id"].'.jpg" alt="image" /></div>';
        }
        $links = array();
        if (!empty($story["url"])) {
            $link = '<a href="'.$story["url"].'">';
            if ($story["source"]) {
                $link .= htmlspecialchars($story["source"]);
            } else {
                $link .= htmlspecialchars($story["url"]);
            }
            $link .= '</a>';
            $links[] = $link;
        }
        if (!empty($story["exhibitions"])) {
            foreach ($story["exhibitions"] as $id=>$exhibition) {
                $links[] = '<a href="/'.$gallery_id.'/exhibition/'.$id.'">'.htmlspecialchars($exhibition).'</a>';
            }
        }
        if (!empty($story["artists"])) {
            foreach ($story["artists"] as $id=>$artist) {
                $links[] = '<a href="/'.$gallery_id.'/artist/'.$id.'">'.htmlspecialchars($artist).'</a>';
            }
        }
        if (!empty($links)) {
            echo '<h4>Links</h4><ul class="links"><li>'.join('</li><li>',$links).'</li></ul>';
        }
        echo '</li>';
    }
} else {
    echo '<li>No news is good news</li>';
}
echo '</ul>';
?>
<script type="text/javascript">
    (function(){
        $(window).on("load",function(){
            var stories = $("ul.news li.story div.text div");
            if (stories.length > 0) {
                stories.each(function(){
                    if (this.scrollHeight > $(this).height()) {
                        $(this).parent().append('<a class="more" href="/<?php echo $gallery_id; ?>/news/'+$(this).data("id")+'">&hellip;</a>');
                    }
                });
            }
        });
    })();
</script>