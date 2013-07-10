<?php
if (empty($lang)) {
    return;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <title>Subscribe to Division Gallery Newsletter</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="/css/style.css" />
</head>
<form action="/news/subscribe" method="post">
    <p><label for="email">Email</label><br /><input type="email" name="email" /></p>
    <?php
    if (!empty($lists)) {
        foreach ($lists as $list) {
            if ($list->status == "ACTIVE") {
                echo '<div><input type="checkbox" name="list[]" value="'.$list->id.'" /><label for="list[]">'.htmlspecialchars($list->name).'</label></div>';
            }
        }
    }
    ?>
    <p><input type="submit" name="subscribe" value="Subscribe" /></p>
</form>
</html>