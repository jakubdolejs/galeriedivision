<div class="subscribe">
    <form action="/<?php echo $gallery_id; ?>/news/subscribe" method="post">
        <h2><?php echo $this->lang->line("Subscribe to Division Gallery newsletter"); ?></h2>
        <div class="container">
            <div class="input"><label for="first_name"><?php echo $this->lang->line("First name"); ?></label><input type="text" name="first_name" /></div>
            <div class="input"><label for="last_name"><?php echo $this->lang->line("Last name"); ?></label><input type="text" name="last_name" /></div>
            <div class="input"><label for="email"><?php echo $this->lang->line("Email"); ?></label><input type="email" name="email" /></div>
            <div><?php
            if (!empty($lists)) {
                foreach ($lists as $list) {
                    echo '<div><input type="checkbox" name="list[]" value="'.$list->id.'" ';
                    if (preg_match('/'.$gallery_id.'/i',$list->name)) {
                        echo 'checked="checked" ';
                    }
                    echo '/> '.htmlspecialchars($list->name).'</div>';
                }
            }
                ?></div>
            <div class="submit"><input type="submit" name="subscribe" value="<?php echo $this->lang->line("Submit"); ?>" /></div>
        </div>
    </form>
</div>