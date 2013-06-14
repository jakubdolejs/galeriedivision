<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Make_nullable extends CI_Migration {

    public function up() {
        $this->dbforge->modify_column("exhibition",array(
            "reception_start"=>array(
                "type"=>"datetime",
                "null"=>true
            )
        ));
        $this->dbforge->modify_column("exhibition",array(
            "reception_end"=>array(
                "type"=>"datetime",
                "null"=>true
            )
        ));
        $this->dbforge->modify_column("exhibition_translation",array(
            "title"=>array(
                "type"=>"varchar",
                "constraint"=>256,
                "null"=>true
            )
        ));
        $this->dbforge->modify_column("exhibition_translation",array(
            "text"=>array(
                "type"=>"text",
                "null"=>true
            )
        ));
        $this->dbforge->modify_column("news_translation",array(
            "text"=>array(
                "type"=>"text",
                "null"=>true
            )
        ));
    }

    public function down() {
        $this->dbforge->modify_column("exhibition",array(
            "reception_start"=>array(
                "type"=>"datetime",
                "null"=>false
            )
        ));
        $this->dbforge->modify_column("exhibition",array(
            "reception_end"=>array(
                "type"=>"datetime",
                "null"=>false
            )
        ));
        $this->dbforge->modify_column("exhibition_translation",array(
            "title"=>array(
                "type"=>"varchar",
                "constraint"=>256,
                "null"=>false
            )
        ));
        $this->dbforge->modify_column("exhibition_translation",array(
            "text"=>array(
                "type"=>"text",
                "null"=>false
            )
        ));
        $this->dbforge->modify_column("news_translation",array(
            "text"=>array(
                "type"=>"text",
                "null"=>false
            )
        ));
    }
}