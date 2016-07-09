<?php namespace WPHPBB\controller;
class Post {
  private $phpbb;

  function __construct() {
    $this->phpbb = new Phpbb();
  }

  public function cross_post_box() {
    \add_meta_box("wphpbb-cross-posting", "WPHPBB Cross posting", array(&$this, "cross_post_box_content"), "post", "side", "low");
  }

  public function cross_post_box_content() {
    echo '<input type="hidden" name="wphpbb_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

    echo "<select name='wphpbb_cross_forum'>";
      foreach($this->get_cross_postable_forums() as $id => $option)
        echo "<option value='{$id}'>{$option}</option>";
    echo "</select>";
    echo "<br />";
    echo "<input type='checkbox' id='wphpbb_do_cross_post' value='wphpbb_do_cross_post'> <label for='wphpbb_do_cross_post'>Cross poster l'article ?</label>";

  }

  private function is_already_cross_posted() {
    global $wpdb;

  }

  private function get_cross_postable_forums() {
    return $this->phpbb->get_cross_postable_forums();
  }



}
?>
