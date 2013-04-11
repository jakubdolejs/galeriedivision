<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_artist extends CI_Migration {
    
    public function up() {
        $this->load->database();
        
        // Artists
        $this->dbforge->add_field(array(
            "id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            ),
            "name"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            )
        ));
        $this->dbforge->add_key("id", TRUE);
        $this->dbforge->create_table("artist");
        
        // Artist gallery link
        $this->dbforge->add_field(array(
            "artist_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            ),
            "gallery_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>64
            ),
            "represented"=>array(
                "type"=>"TINYINT",
                "constraint"=>1,
                "null"=>FALSE,
                "default"=>1
            )
        ));
        $this->dbforge->add_key("artist_id", TRUE);
        $this->dbforge->add_key("gallery_id", TRUE);
        $this->dbforge->create_table("artist_gallery");
        $this->db->query("ALTER TABLE artist_gallery ADD FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE artist_gallery ADD FOREIGN KEY (gallery_id) REFERENCES gallery (id) ON DELETE CASCADE ON UPDATE CASCADE");
                
        // Exhibition artists link
        $this->dbforge->add_field(array(
            "artist_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            ),
            "exhibition_id"=>array(
                "type"=>"VARCHAR",
                "constraint"=>128
            )
        ));
        $this->dbforge->add_key("exhibition_id",TRUE);
        $this->dbforge->add_key("artist_id",TRUE);
        $this->dbforge->create_table("artist_exhibition");
        $this->db->query("ALTER TABLE artist_exhibition ADD FOREIGN KEY (exhibition_id) REFERENCES exhibition (id) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE artist_exhibition ADD FOREIGN KEY (artist_id) REFERENCES artist (id) ON DELETE CASCADE ON UPDATE CASCADE");
        
        $this->db->insert_batch("artist",array(
            array(
                "id"=>"barry-allikas",
                "name"=>"Barry Allikas"
            ),
            array(
                "id"=>"stephen-andrews",
                "name"=>"Stephen Andrews"
            ),
            array(
                "id"=>"nicolas-baier",
                "name"=>"Nicolas Baier"
            ),
            array(
                "id"=>"bonnie-baxter",
                "name"=>"Bonnie Baxter"
            ),
            array(
                "id"=>"martin-bourdeau",
                "name"=>"Martin Bourdeau"
            ),
            array(
                "id"=>"john-brown",
                "name"=>"John Brown"
            ),
            array(
                "id"=>"michel-de-broin",
                "name"=>"Michel De Broin"
            ),
            array(
                "id"=>"manon-de-pauw",
                "name"=>"Manon De Pauw"
            ),
            array(
                "id"=>"michael-dumontier-neil-farber",
                "name"=>"Michael Dumontier & Neil Farber"
            ),
            array(
                "id"=>"michael-dumontier",
                "name"=>"Michael Dumontier"
            ),
            array(
                "id"=>"neil-farber",
                "name"=>"Neil Farber"
            ),
            array(
                "id"=>"isabelle-hayeur",
                "name"=>"Isabelle Hayeur"
            ),
            array(
                "id"=>"simon-hughes",
                "name"=>"Simon Hughes"
            ),
            array(
                "id"=>"sarah-anne-johnson",
                "name"=>"Sarah Anne Johnson"
            ),
            array(
                "id"=>"wanda-koop",
                "name"=>"Wanda Koop"
            ),
            array(
                "id"=>"vincent-lafrance",
                "name"=>"Vincent Lafrance"
            ),
            array(
                "id"=>"mathieu-lefevre",
                "name"=>"Mathieu Lefevre"
            ),
            array(
                "id"=>"allison-schulnik",
                "name"=>"Allison Schulnik"
            ),
            array(
                "id"=>"richard-max-tremblay",
                "name"=>"Richard-Max Tremblay"
            ),
            array(
                "id"=>"etienne-zack",
                "name"=>"Etienne Zack"
            ),
            array(
                "id"=>"mike-bayne",
                "name"=>"Mike Bayne"
            ),
            array(
                "id"=>"james-carl",
                "name"=>"James Carl"
            ),
            array(
                "id"=>"kim-dorland",
                "name"=>"Kim Dorland"
            ),
            array(
                "id"=>"marcel-dzama",
                "name"=>"Marcel Dzama"
            ),
            array(
                "id"=>"andre-ethier",
                "name"=>"Andre Ethier"
            ),
            array(
                "id"=>"karel-funk",
                "name"=>"Karel Funk"
            ),
            array(
                "id"=>"tim-gardner",
                "name"=>"Tim Gardner"
            ),
            array(
                "id"=>"myfanwy-macleod",
                "name"=>"Myfanwy Macleod"
            ),
            array(
                "id"=>"elizabeth-mcintosh",
                "name"=>"Elizabeth Mcintosh"
            ),
            array(
                "id"=>"jonathan-pylypchuk",
                "name"=>"Jonathan Pylypchuk"
            ),
            array(
                "id"=>"marc-seguin",
                "name"=>"Marc Séguin"
            ),
            array(
                "id"=>"adrian-williams",
                "name"=>"Adrian Williams"
            )
        ));
        $this->db->insert_batch("artist_gallery",array(
            array(
                "artist_id"=>"barry-allikas",
                "gallery_id"=>"montreal",
                "represented"=>1
            ),
            array(
                "artist_id"=>"stephen-andrews",
                "gallery_id"=>"montreal",
                "represented"=>1
            ),
            array(
                "artist_id"=>"nicolas-baier",
                "gallery_id"=>"montreal",
                "represented"=>1
            ),
            array(
                "artist_id"=>"bonnie-baxter",
                "gallery_id"=>"montreal",
                "represented"=>1
            ),
            array(
                "artist_id"=>"martin-bourdeau",
                "gallery_id"=>"montreal",
                "represented"=>1
            ),
            array(
                "artist_id"=>"john-brown",
                "gallery_id"=>"montreal",
                "represented"=>1
            ),
            array(
                "artist_id"=>"michel-de-broin",
                "gallery_id"=>"montreal",
                "represented"=>1
            ),
            array(
                "artist_id"=>"manon-de-pauw",
                "gallery_id"=>"montreal",
                "represented"=>1
            ),
            array(
                "artist_id"=>"michael-dumontier-neil-farber",
                "gallery_id"=>"montreal",
                "represented"=>1
            ),
            array(
                "artist_id"=>"michael-dumontier",
                "gallery_id"=>"montreal",
                "represented"=>1
            ),
            array(
                "artist_id"=>"neil-farber",
                "gallery_id"=>"montreal",
                "represented"=>1
            ),
            array(
                "artist_id"=>"isabelle-hayeur",
                "gallery_id"=>"montreal",
                "represented"=>1
            ),
            array(
                "artist_id"=>"simon-hughes",
                "gallery_id"=>"montreal",
                "represented"=>1
            ),
            array(
                "artist_id"=>"sarah-anne-johnson",
                "gallery_id"=>"montreal",
                "represented"=>1
            ),
            array(
                "artist_id"=>"wanda-koop",
                "gallery_id"=>"montreal",
                "represented"=>1
            ),
            array(
                "artist_id"=>"vincent-lafrance",
                "gallery_id"=>"montreal",
                "represented"=>1
            ),
            array(
                "artist_id"=>"mathieu-lefevre",
                "gallery_id"=>"montreal",
                "represented"=>1
            ),
            array(
                "artist_id"=>"allison-schulnik",
                "gallery_id"=>"montreal",
                "represented"=>1
            ),
            array(
                "artist_id"=>"richard-max-tremblay",
                "gallery_id"=>"montreal",
                "represented"=>1
            ),
            array(
                "artist_id"=>"etienne-zack",
                "gallery_id"=>"montreal",
                "represented"=>1
            ),
            //Also Showing Works By
            array(
                "artist_id"=>"mike-bayne",
                "gallery_id"=>"montreal",
                "represented"=>0
            ),
            array(
                "artist_id"=>"james-carl",
                "gallery_id"=>"montreal",
                "represented"=>0
            ),
            array(
                "artist_id"=>"kim-dorland",
                "gallery_id"=>"montreal",
                "represented"=>0
            ),
            array(
                "artist_id"=>"marcel-dzama",
                "gallery_id"=>"montreal",
                "represented"=>0
            ),
            array(
                "artist_id"=>"andre-ethier",
                "gallery_id"=>"montreal",
                "represented"=>0
            ),
            array(
                "artist_id"=>"karel-funk",
                "gallery_id"=>"montreal",
                "represented"=>0
            ),
            array(
                "artist_id"=>"tim-gardner",
                "gallery_id"=>"montreal",
                "represented"=>0
            ),
            array(
                "artist_id"=>"myfanwy-macleod",
                "gallery_id"=>"montreal",
                "represented"=>0
            ),
            array(
                "artist_id"=>"elizabeth-mcintosh",
                "gallery_id"=>"montreal",
                "represented"=>0
            ),
            array(
                "artist_id"=>"jonathan-pylypchuk",
                "gallery_id"=>"montreal",
                "represented"=>0
            ),
            array(
                "artist_id"=>"marc-seguin",
                "gallery_id"=>"montreal",
                "represented"=>0
            ),
            array(
                "artist_id"=>"adrian-williams",
                "gallery_id"=>"montreal",
                "represented"=>0
            )
        ));
        
        $this->db->insert_batch("artist_exhibition",array(
            array(
                "artist_id"=>"michael-dumontier-neil-farber",
                "exhibition_id"=>"after-the-royal-art-lodge"
            ),
            array(
                "artist_id"=>"marcel-dzama",
                "exhibition_id"=>"after-the-royal-art-lodge"
            ),
            array(
                "artist_id"=>"jonathan-pylypchuk",
                "exhibition_id"=>"after-the-royal-art-lodge"
            ),
            array(
                "artist_id"=>"adrian-williams",
                "exhibition_id"=>"after-the-royal-art-lodge"
            )
        ));
    }
    
    public function down() {
        $this->db->query("ALTER TABLE artist_gallery DROP FOREIGN KEY (artist_id)");
        $this->db->query("ALTER TABLE artist_gallery DROP FOREIGN KEY (gallery_id)");
        $this->db->query("ALTER TABLE artist_exhibition DROP FOREIGN KEY (exhibition_id)");
        $this->db->query("ALTER TABLE artist_exhibition DROP FOREIGN KEY (artist_id)");
        $this->dbforge->drop_table("artist");
        $this->dbforge->drop_table("artist_gallery");
        $this->dbforge->drop_table("artist_exhibition");
    }
}