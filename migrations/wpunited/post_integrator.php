<?php namespace wphpbb\migrations\wpunited;

class Post_integrator {
  function __construct() {

  }

  public function do_integration() {
    $topics = $this->get_all_xpost_topics();
    foreach($topics as $topic) {
      $this->handle_post($topic);
      echo "<hr />";
    }
  }

  private function handle_post($topic) {
    if(\wphpbb\controller\post::is_already_cross_posted($topic->topic_wpu_xpost) != true) {
      echo "should cross post post {$topic->topic_wpu_xpost} to {$topic->topic_id} <br />";
      echo \wphpbb\controller\post::set_is_crossposted($topic->topic_wpu_xpost, $topic->topic_id);
    }
    else {
      echo "cross-post {$topic->topic_wpu_xpost} already to {$topic->topic_id} <br />";
      if(\wphpbb\controller\post::get_cross_posted_topic_id($topic->topic_wpu_xpost) === $topic->topic_id)
        return true;
      //else
        //throw new \Exception("Cross-post does not match");
    }
  }

  // TO DO
  // GET TABLE NAME
  private function get_all_xpost_topics() {
    global $wpdb;

    $sql = "SELECT topic_id, topic_wpu_xpost FROM testfo_topics WHERE topic_wpu_xpost > 1";
    return $wpdb->get_results($sql);
  }

}



?>
