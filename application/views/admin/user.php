<?php
$this->load->helper("form");
if ($logged_in_user["superuser"]) {
    echo form_open(empty($user) ? "/admin/user/add" : "/admin/user/edit",array("method"=>"post"));
    echo '<p>'.form_label("Email","id").'<br /><input name="id" type="email" value="'.(!empty($user) ? $user["id"] : "").'" /></p>';
    echo '<p>Galleries the user can edit<div id="galleries"></div></p>';
    if (!empty($selected_galleries)) {
        foreach ($selected_galleries as $gid) {
            echo '<input type="hidden" name="gallery_ids[]" value="'.$gid.'" />';
        }
    }
    $extra = "";
    if (!empty($user) && $user["superuser"] && $user["id"] != $logged_in_user["id"]) {
        $extra = 'disabled="disabled"';
    }
    echo '<p>'.form_checkbox("superuser","1",!empty($user) && $user["superuser"],$extra).' '.form_label("Administrator privileges","superuser").'</p>';
    echo '<p>'.form_submit("save","Save").'</p>';
    echo form_close();
    if (!empty($user)) {
        echo form_open("/admin/user/password",array("method"=>"post"));
        echo '<input type="hidden" name="id" value="'.$user["id"].'" />';
        echo '<p><a href="javascript:void(0)" class="submit">Reset password</a></p>';
        echo form_close();
    }
    echo '<script type="text/javascript">
        (function(){
            var gallerySelector = new DivisionAdmin.MultipleItemSelector('.json_encode($galleries).','.json_encode($selected_galleries).');
            gallerySelector.changeCallback = function(galleries) {
                $("input[name=\'gallery_ids[]\']").remove();
                for (var i=0; i<galleries.length; i++) {
                    $(\'<input type="hidden" name="gallery_ids[]" value="\'+galleries[i].id+\'" />\').appendTo($("#galleries"));
                }
            }
            gallerySelector.appendTo($("#galleries"));
            $("a.submit").on("click",function(){
                $(this).parents("form").get(0).submit();
            });
        })();
    </script>';
} else {
    echo '<p>User name: '.$user["id"].'</p>';
    if (!empty($user) && $user["id"] == $logged_in_user["id"]) {
        $this->load->view("admin/set_password",array('user'=>$user));
    }
}
?>
