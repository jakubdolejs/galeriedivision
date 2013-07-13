<form action="/news/subscribe" method="post">
    <h2><?php echo $this->lang->line("Subscribe to Division Gallery newsletter"); ?></h2>
    <div class="input"><label for="first_name"><?php echo $this->lang->line("First name"); ?></label><input type="text" name="first_name" /></div>
    <div class="input"><label for="last_name"><?php echo $this->lang->line("Last name"); ?></label><input type="text" name="last_name" /></div>
    <div class="input"><label for="email"><?php echo $this->lang->line("Email"); ?></label><input type="email" name="email" /></div>
    <div class="submit"><input type="submit" name="subscribe" value="<?php echo $this->lang->line("Submit"); ?>" /></div>
</form>