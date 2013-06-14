<?php
$this->load->helper("form");
echo form_open("/admin/artists",array("method"=>"post"));
echo '<p>'.form_label("Name","name").'<br />'.form_input("name").' '.form_submit("add","Add").'</p>';
echo form_close();