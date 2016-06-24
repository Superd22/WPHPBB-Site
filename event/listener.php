<?php namespace wphpbb\event;
class listener {

  public function __construct() {
    $this->add_actions();
  }

  private function add_actions() {
    \add_action('admin_menu', array(new \wphpbb\controller\Post(), 'cross_post_box'));
  }

}
?>
