<?php
class MemcacheKeys {
    public static function news($gallery_id,$lang) {
        return "news_".$gallery_id."_".$lang;
    }

    public static function news_story($gallery_id,$lang,$news_id) {
        return "news_".$gallery_id."_".$lang."_".$news_id;
    }

    public static function artists($gallery_id,$lang) {
        return "artists_".$gallery_id."_".$lang;
    }

    public static function artist($gallery_id,$artist_id,$lang) {
        return "artist_".$gallery_id."_".$artist_id."_".$lang;
    }

    public static function artist_cv($gallery_id,$artist_id,$lang) {
        return "artist_cv_".$gallery_id."_".$artist_id."_".$lang;
    }

    public static function artist_exhibitions($gallery_id,$artist_id,$lang) {
        return "artist_exhibitions_".$gallery_id."_".$artist_id."_".$lang;
    }

    public static function exhibition($gallery_id,$exhibition_id,$lang) {
        return "exhibition_".$gallery_id."_".$exhibition_id."_".$lang;
    }

    public static function artist_image($gallery_id,$artist_id,$image_id,$lang) {
        return "artist_image_".$gallery_id."_".$artist_id."_".$image_id."_".$lang;
    }

    public static function contact($gallery_id,$lang) {
        return "contact_".$gallery_id."_".$lang;
    }
}