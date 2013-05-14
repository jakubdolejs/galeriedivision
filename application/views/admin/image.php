<?php
if (empty($image) || empty($artists)) {
    return;
}
$this->load->helper("form");
echo form_open("/admin/image/".$image["id"],array("method"=>"post","id"=>"imageForm".$image["id"]));
echo '<p><img src="/images/185/'.$image["id"].'.jpg" alt="image" /></p>';
echo '<p>'.form_fieldset("Dimensions (inches)");
echo '<p>'.form_label("Width","width").'<br />'.form_input("width",$image["width"]).'</p>';
echo '<p>'.form_label("Height","height").'<br />'.form_input("height",$image["height"]).'</p>';
echo '<p>'.form_label("Depth","depth").'<br />'.form_input("depth",$image["depth"]).'</p>';
echo form_fieldset_close().'</p>';
echo '<p>'.form_label("Year","year").'<br />'.form_input("year",$image["creation_year"]).'</p>';
$options = array();
foreach ($artists as $artist) {
    $options[$artist["id"]] = $artist["name"];
}
$selected = array();
if (!empty($image["artists"])) {
    $selected = array_keys($image["artists"]);
}
echo '<p>'.form_label("Artist(s)","artists[]").'<br />'.form_multiselect("artists[]",$options,$selected).'</p>';
echo '<p>'.form_fieldset("Title");
echo '<p>'.form_label("English","title[en]").'<br />'.form_input("title[en]",@$image["title"]["en"],'lang="en"').'</p>';
echo '<p>'.form_label("French","title[fr]").'<br />'.form_input("title[fr]",@$image["title"]["fr"],'lang="fr"').'</p>';
echo form_fieldset_close().'</p>';
echo '<p>'.form_fieldset("Description");
echo '<p>'.form_label("English","description[en]").'<br />'.form_textarea("description[en]",@$image["description"]["en"],'lang="en"').'</p>';
echo '<p>'.form_label("French","description[fr]").'<br />'.form_textarea("description[fr]",@$image["description"]["fr"],'lang="fr"').'</p>';
echo form_fieldset_close().'</p>';
echo '<p>'.form_submit('save','Save').'</p>';
echo form_close();
?>
<script type="text/javascript">
    //<![CDATA[
    var form = $("#imageForm<?php echo $image["id"]; ?>");
    form.find("input[name='title[en]'],input[name='title[fr]'],textarea[name='description[en]'],textarea[name='description[fr]']").on("change",function(){
        var params = /^(.+)\[(.+)\]$/.exec($(this).attr("name"));
        var name = params[1];
        var lang = params[2];
        var otherLang = lang == "en" ? "fr" : "en";
        var otherField = form.find("*[name='"+name+"["+otherLang+"]']");
        if ($.trim($(this).val()).length > 0 && $.trim(otherField.val()).length == 0) {
            otherField.val($(this).val());
        }
    });
    //]]>
</script>