<?php namespace WPHPBB\event;
class listener {

  public function __construct() {
    $this->add_actions();
  }

  private function add_actions() {
    $settings = \WPHPBB\controller\Settings::get_settings();
    if(isset($settings["wordpress"]["activate"]) && $settings["wordpress"]["activate"] === "on") {
      // Posting
      \add_action('admin_menu', array(new \WPHPBB\controller\Post(), 'cross_post_box'));
      \add_action('save_post', array(new \WPHPBB\controller\Post(), 'do_cross_post'), 10, 3 );

      // Users
      \add_action('wp_login', array(new \WPHPBB\controller\User(), 'login_phpbb'), 10 , 2);
    }

    // Admin
    \add_action( 'admin_menu', array(new \WPHPBB\admin\AdminSettings(), "wphpbb_menu"));
  }

}
?>
