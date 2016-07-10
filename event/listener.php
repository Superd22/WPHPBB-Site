<?php namespace wphpbb\event;
class listener {

  public function __construct() {
    $this->add_actions();
  }

  private function add_actions() {
    // Posting
    \add_action('admin_menu', array(new \wphpbb\controller\Post(), 'cross_post_box'));
    \add_action('save_post', array(new \wphpbb\controller\Post(), 'do_cross_post'), 10, 3 );


    // Users
    \add_action('wp_login', array(new \wphpbb\controller\User(), 'login_phpbb'), 10 , 2);
  }

}
?>
