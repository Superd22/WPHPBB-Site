<?php namespace WPHPBB\controller;
class Post {

  function __construct() {

  }

  public function cross_post_box() {
    \add_meta_box("wphpbb-cross-posting", "WPHPBB Cross posting", array(&$this, "cross_post_box_content"), "post", "side", "low");
  }

  public function cross_post_box_content() {
    echo '<input type="hidden" name="wphpbb_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';
  }

  private function is_already_cross_posted() {
    global $wpdb;

  }



}
?>
