<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once(rtrim(APPPATH,"/")."/models/GD_Model.php");

class News_model extends GD_Model {
    
    public function get_news($gallery_id=null,$lang=null,$max_stories=0) {
        $this->db->select("news.id, headline, news_translation.lang, news_translation.text, source, date_published, url, artist_id, name, news_exhibition.exhibition_id, exhibition_translation.lang as 'exhibition_lang', title, news_image.image_id, news_gallery.gallery_id")
            ->from("news")
            ->join("news_gallery", "news_gallery.news_id = news.id")
            ->join("news_image", "news_image.news_id = news.id", "left")
            ->join("news_translation", "news_translation.news_id = news.id","left")
            ->join("news_artist JOIN artist ON news_artist.artist_id = artist.id", "news_artist.news_id = news.id","left")
            ->join("news_exhibition JOIN exhibition_translation ON news_exhibition.exhibition_id = exhibition_translation.exhibition_id", "news_exhibition.news_id = news.id","left");
        if ($gallery_id) {
            $this->db->where("news_gallery.gallery_id",$gallery_id);
        }
        $this->db->order_by("date_published","DESC")
            ->order_by("artist.name");
        $query = $this->db->get();
        $news = array();
        if ($query->num_rows()) {
            foreach ($query->result_array() as $row) {
                if (!isset($news[$row["id"]])) {
                    $news[$row["id"]] = array(
                        "id"=>$row["id"],
                        "headline"=>array(),
                        "text"=>array(),
                        "source"=>$row["source"],
                        "date_published"=>$row["date_published"],
                        "url"=>$row["url"],
                        "artists"=>array(),
                        "exhibitions"=>array(),
                        "galleries"=>array()
                    );
                    if (!isset($news[$row["id"]]["image_id"]) && !empty($row["image_id"])) {
                        $news[$row["id"]]["image_id"] = $row["image_id"];
                    }
                }
                if (!isset($news[$row["id"]]["headline"][$row["lang"]]) && !empty($row["headline"])) {
                    $news[$row["id"]]["headline"][$row["lang"]] = $row["headline"];
                }
                if (!isset($news[$row["id"]]["text"][$row["lang"]]) && !empty($row["text"])) {
                    $news[$row["id"]]["text"][$row["lang"]] = $row["text"];
                }
                if (!isset($news[$row["id"]]["exhibitions"][$row["exhibition_id"]][$row["exhibition_lang"]]) && !empty($row["exhibition_id"])) {
                    $news[$row["id"]]["exhibitions"][$row["exhibition_id"]][$row["exhibition_lang"]] = $row["title"];
                }
                if (!isset($news[$row["id"]]["artists"][$row["artist_id"]]) && !empty($row["artist_id"])) {
                    $news[$row["id"]]["artists"][$row["artist_id"]] = $row["name"];
                }
                if (!in_array($row["gallery_id"],$news[$row["id"]]["galleries"])) {
                    $news[$row["id"]]["galleries"][] = $row["gallery_id"];
                }
            }
            if ($lang) {
                foreach ($news as $id=>$story) {
                    if (!empty($story["headline"][$lang])) {
                        $news[$id]["headline"] = $story["headline"][$lang];
                    } else {
                        $news[$id]["headline"] = join("/",array_unique(array_values($story["headline"])));
                    }
                    if (!empty($story["text"][$lang])) {
                        $news[$id]["text"] = $story["text"][$lang];
                    } else {
                        $news[$id]["text"] = join("/",array_unique(array_values($story["text"])));
                    }
                    foreach ($story["exhibitions"] as $ex_id=>$exhibition) {
                        if (!empty($exhibition[$lang])) {
                            $news[$id]["exhibitions"][$ex_id] = $exhibition[$lang];
                        } else {
                            $news[$id]["exhibitions"][$ex_id] = join("/",array_unique(array_values($exhibition)));
                        }
                    }
                }
            }
            $news = array_values($news);
            if ($max_stories > 0) {
                array_splice($news,0,$max_stories);
            }
        }
        return $news;
    }

    public function get_story($id,$lang=null) {
        $this->db->select("news.id, headline, news_translation.lang, news_translation.text, source, date_published, url, artist_id, name, news_exhibition.exhibition_id, exhibition_translation.lang as 'exhibition_lang', title, news_image.image_id, news_gallery.gallery_id")
            ->from("news")
            ->join("news_gallery", "news_gallery.news_id = news.id")
            ->join("news_image", "news_image.news_id = news.id", "left")
            ->join("news_translation", "news_translation.news_id = news.id","left")
            ->join("news_artist JOIN artist ON news_artist.artist_id = artist.id", "news_artist.news_id = news.id","left")
            ->join("news_exhibition JOIN exhibition_translation ON news_exhibition.exhibition_id = exhibition_translation.exhibition_id", "news_exhibition.news_id = news.id","left")
            ->where("news.id",$id)
            ->order_by("artist.name");
        $query = $this->db->get();
        $news = array();
        if ($query->num_rows()) {
            foreach ($query->result_array() as $row) {
                if (empty($news)) {
                    $news = array(
                        "id"=>$row["id"],
                        "headline"=>array(),
                        "text"=>array(),
                        "source"=>$row["source"],
                        "date_published"=>$row["date_published"],
                        "url"=>$row["url"],
                        "artists"=>array(),
                        "exhibitions"=>array(),
                        "galleries"=>array()
                    );
                    if (!isset($news["image_id"]) && !empty($row["image_id"])) {
                        $news["image_id"] = $row["image_id"];
                    }
                }
                if (!isset($news["headline"][$row["lang"]]) && !empty($row["headline"])) {
                    $news["headline"][$row["lang"]] = $row["headline"];
                }
                if (!isset($news["text"][$row["lang"]]) && !empty($row["text"])) {
                    $news["text"][$row["lang"]] = $row["text"];
                }
                if (!isset($news["exhibitions"][$row["exhibition_id"]][$row["exhibition_lang"]]) && !empty($row["exhibition_id"])) {
                    $news["exhibitions"][$row["exhibition_id"]][$row["exhibition_lang"]] = $row["title"];
                }
                if (!isset($news["artists"][$row["artist_id"]]) && !empty($row["artist_id"])) {
                    $news["artists"][$row["artist_id"]] = $row["name"];
                }
                if (!in_array($row["gallery_id"],$news["galleries"])) {
                    $news["galleries"][] = $row["gallery_id"];
                }
            }
            if ($lang) {
                if (!empty($news["headline"][$lang])) {
                    $news["headline"] = $news["headline"][$lang];
                } else {
                    $news["headline"] = join("/",array_unique(array_values($news["headline"])));
                }
                if (!empty($news["text"][$lang])) {
                    $news["text"] = $news["text"][$lang];
                } else {
                    $news["text"] = join("/",array_unique(array_values($news["text"])));
                }
                foreach ($news["exhibitions"] as $ex_id=>$exhibition) {
                    if (!empty($exhibition[$lang])) {
                        $news["exhibitions"][$ex_id] = $exhibition[$lang];
                    } else {
                        $news["exhibitions"][$ex_id] = join("/",array_unique(array_values($exhibition)));
                    }
                }
            }
        }
        return $news;
    }

    public function add($user_id,$headline,$text,$source,$date,$url,$gallery_ids,$artist_ids,$exhibition_ids,$image_id) {
        $this->db->set("source",$source)
            ->set("date_published",$date)
            ->set("url",$url);
        if ($this->db->insert("news") !== false) {
            $this->log($user_id);
            $id = $this->db->insert_id();
            $translation = array();
            if (!empty($headline)) {
                foreach ($headline as $lang=>$value) {
                    $translation[$lang] = array(
                        "lang"=>$lang,
                        "headline"=>$value,
                        "news_id"=>$id
                    );
                }
            }
            if (!empty($text)) {
                foreach ($text as $lang=>$value) {
                    if (!isset($translation[$lang])) {
                        $translation[$lang] = array(
                            "lang"=>$lang,
                            "text"=>$value,
                            "news_id"=>$id
                        );
                    } else {
                        $translation[$lang]["text"] = $value;
                    }
                }
            }
            if (!empty($translation)) {
                $translation = array_values($translation);
                $this->db->insert_batch("news_translation",$translation);
                $this->log($user_id);
            }
            if (!empty($artist_ids)) {
                $artists = array();
                foreach ($artist_ids as $artist_id) {
                    $artists[] = array(
                        "artist_id"=>$artist_id,
                        "news_id"=>$id
                    );
                }
                $this->db->insert_batch("news_artist",$artists);
                $this->log($user_id);
            }
            if (!empty($exhibition_ids)) {
                $exhibitions = array();
                foreach ($exhibition_ids as $exhibition_id) {
                    $exhibitions[] = array(
                        "exhibition_id"=>$exhibition_id,
                        "news_id"=>$id
                    );
                }
                $this->db->insert_batch("news_exhibition",$exhibitions);
                $this->log($user_id);
            }
            if (!empty($gallery_ids)) {
                $galleries = array();
                foreach ($gallery_ids as $gallery_id) {
                    $galleries[] = array(
                        "gallery_id"=>$gallery_id,
                        "news_id"=>$id
                    );
                }
                $this->db->insert_batch("news_gallery",$galleries);
                $this->log($user_id);
            }
            if (!empty($image_id)) {
                $this->db->set("image_id",$image_id)
                    ->set("news_id",$id);
                $this->db->insert("news_image");
                $this->log($user_id);
            }
            $this->cache->memcached->clean();
            return $id;
        }
        return null;
    }

    public function update($user_id,$id,$headline,$text,$source,$date,$url,$gallery_ids,$artist_ids,$exhibition_ids,$image_id) {
        $this->db->trans_start();
        $this->db->set("source",$source)
            ->set("date_published",$date)
            ->set("url",$url)
            ->where("id",$id);
        $this->db->update("news");
        $this->log($user_id);

        $this->db->where("news_id",$id);
        $this->db->delete("news_translation");
        $this->log($user_id);
        $translation = array();
        if (!empty($headline)) {
            foreach ($headline as $lang=>$value) {
                $translation[$lang] = array(
                    "lang"=>$lang,
                    "headline"=>$value,
                    "news_id"=>$id
                );
            }
        }
        if (!empty($text)) {
            foreach ($text as $lang=>$value) {
                if (!isset($translation[$lang])) {
                    $translation[$lang] = array(
                        "lang"=>$lang,
                        "text"=>$value,
                        "news_id"=>$id
                    );
                } else {
                    $translation[$lang]["text"] = $value;
                }
            }
        }
        if (!empty($translation)) {
            $translation = array_values($translation);
            $this->db->insert_batch("news_translation",$translation);
            $this->log($user_id);
        }

        $this->db->where("news_id",$id);
        $this->db->delete("news_artist");
        $this->log($user_id);
        if (!empty($artist_ids)) {
            $artists = array();
            foreach ($artist_ids as $artist_id) {
                $artists[] = array(
                    "artist_id"=>$artist_id,
                    "news_id"=>$id
                );
            }
            $this->db->insert_batch("news_artist",$artists);
            $this->log($user_id);
        }

        $this->db->where("news_id",$id);
        $this->db->delete("news_exhibition");
        $this->log($user_id);
        if (!empty($exhibition_ids)) {
            $exhibitions = array();
            foreach ($exhibition_ids as $exhibition_id) {
                $exhibitions[] = array(
                    "exhibition_id"=>$exhibition_id,
                    "news_id"=>$id
                );
            }
            $this->db->insert_batch("news_exhibition",$exhibitions);
            $this->log($user_id);
        }

        $this->db->where("news_id",$id);
        $this->db->delete("news_gallery");
        $this->log($user_id);
        if (!empty($gallery_ids)) {
            $galleries = array();
            foreach ($gallery_ids as $gallery_id) {
                $galleries[] = array(
                    "gallery_id"=>$gallery_id,
                    "news_id"=>$id
                );
            }
            $this->db->insert_batch("news_gallery",$galleries);
            $this->log($user_id);
        }

        $this->db->where("news_id",$id);
        $this->db->delete("news_image");
        $this->log($user_id);
        if (!empty($image_id)) {
            $this->db->set("image_id",$image_id)
                ->set("news_id",$id);
            $this->db->insert("news_image");
            $this->log($user_id);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status() !== false) {
            $this->cache->memcached->clean();
            return true;
        }
        return false;
    }

    public function delete($user_id,$id) {
        $this->db->trans_start();
        $this->db->where("news_id",$id);
        $this->db->delete("news_image");
        $this->log($user_id);
        $this->db->where("news_id",$id);
        $this->db->delete("news_gallery");
        $this->log($user_id);
        $this->db->where("news_id",$id);
        $this->db->delete("news_exhibition");
        $this->log($user_id);
        $this->db->where("news_id",$id);
        $this->db->delete("news_artist");
        $this->log($user_id);
        $this->db->where("news_id",$id);
        $this->db->delete("news_translation");
        $this->log($user_id);
        $this->db->where("id",$id);
        $this->db->delete("news");
        $this->log($user_id);
        $this->db->trans_complete();
        if ($this->db->trans_status() !== false) {
            $this->cache->memcached->clean();
            return true;
        }
        return false;
    }
}