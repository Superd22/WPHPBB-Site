<?php namespace WPHPBB\helper;
  class Post {
     function __construct($_post_id = false) {
       if($_post_id && $_post_id > 0) {
         $this->post_id = $_post_id;
       }
       else {
           if(\in_the_loop() && \get_the_ID()) $this->post_id = \get_the_ID();
           else throw new \Exception("No post id could be found");
       }
     }

     public function get_topic_id() {
       if(!isset($this->topic_id)) {
         if($this->is_cross_posted())
          $this->topic_id = \WPHPBB\controller\Post::get_cross_posted_topic_id($this->post_id);
        else $this->topic_id = false;
      }
       return $this->topic_id;
     }

     public function is_cross_posted() {
       return \WPHPBB\controller\Post::is_already_cross_posted($this->post_id);
     }

     // TO DO TABLE NAME
     public function get_topic_details() {
       global $wpdb;
       if($this->is_cross_posted()) {
         $topic_id = $this->get_topic_id();
         $topics = $wpdb->get_results("SELECT forum_id FROM testfo_topics WHERE topic_id='" . $topic_id . "'  LIMIT 1");

         return array(
           "topic_id"      => $topic_id,
           "forum_id"      => $topics["forum_id"],
         );
       }
       else return false;
     }

     public function get_topic_comments_number() {
       global $wpdb;
       if($this->is_cross_posted()) {
         $topic_id = $this->get_topic_id();
         $sql = "SELECT COUNT(DISTINCT post_id) FROM testfo_posts WHERE topic_id='" . $topic_id . "'";
         $posts = $wpdb->get_results($sql, 'ARRAY_N');

         return $posts[0][0] - 1;
       }
       else return false;
     }
  }
?>
