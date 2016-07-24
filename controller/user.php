<?php namespace wphpbb\controller;
class User {
  private $id;
  private $phpbb;

  function __construct($id = 0, &$phpbb = false) {
    if($phpbb !== false) $this->phpbb = $phpbb;

    $this->id = $id;
  }

  function ensure_phpbb() {
    if(!$this->phpbb) {
      $this->phpbb = new Phpbb();
    }
  }

  public function is_current_user() {
    if($this->id > 1 && $this->id === \get_current_user_id()) return true;
    else return false;
  }

  public function get_phpbb_id() {
    return (integer) get_user_meta( $this->id , "_wphpbb_forum_user_id" , true);
  }

  public function phpbb_switch_to() {
    $this->ensure_phpbb();
    $this->phpbb->make_phpbb_env();

    $this->phpbb->transition_user($this->get_phpbb_id(), "127.0.0.1");
  }

  public function phpbb_switch_back() {
    $this->phpbb->transition_user();
  }

  public function login_phpbb( $user_login, $user ) {

  }

  public function register_wordpress() {

  }

  public function register_phpbb() {

  }

}

?>
