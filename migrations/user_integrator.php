<?php namespace WPHPBB\migrations;

class UserIntegrator {
  private $phpbb;
  private $hadWPunited;

  function __construct() {
    $this->phpbb = new \WPHPBB\controller\Phpbb();
    $this->phpbb->make_phpbb_env();
    $this->check_wp_united();
  }

  public function do_migration() {
    foreach($this->get_all_phpbb_users() as $phpbb_user)
      $this->check_phpbb_user($phpbb_user);

    // At this point ALL phpbb users should have a wordpress equivalent
    foreach($this->get_all_wp_users() as $wp_user) {
      if(!$this->is_in_phpbb($wp_user))
      if(!$this->find_phpbb_equivalent($wp_user))
      $this->phpbb_create($wp_user);
    }


  }

  private function check_phpbb_user($phpbb_user) {
    set_time_limit(10);
    echo $phpbb_user["user_id"]." : ".$phpbb_user["username"]."<br />";
    if(!$this->is_in_wordpress($phpbb_user))
    if(!$this->wp_united_migration($phpbb_user))
    if(!$this->find_wp_equivalent($phpbb_user))
    $this->wp_create($phpbb_user);

    echo "</hr>";
  }

  private function check_wp_united() {
    $sql = 'SHOW COLUMNS FROM ' . USERS_TABLE . ' LIKE "user_wpuint_id"';
    $data = $this->phpbb->db->sql_query($sql);
    $result = $this->phpbb->db->sql_fetchrow($data);

    if(isset($result["Field"]) && $result["Field"] === "user_wpuint_id") $this->hadWPunited = true;
    else $this->hadWPunited = false;
  }

  private function get_all_phpbb_users() {
    $sql = 'SELECT user_id, username, user_email
    FROM ' . USERS_TABLE . ' WHERE user_id > 1';

    $result = $this->phpbb->db->sql_query($sql);

    return $this->phpbb->db->sql_fetchrowset($result);
  }

  private function get_all_wp_users() {

  }

  private function find_wp_equivalent($phpbb_user) {
    $email_users = get_users(array(
      "search" => $phpbb_user["user_email"],
    ));

    if(sizeof($email_users) === 1)
      $user = $email_users[0];
    else {
      $args = array("search" => $phpbb_user["username"]);
      if(sizeof($email_users) > 1) {
          foreach($email_users as $email)
            $ids[] = $email->data->ID;

          $args["include"] = $ids;
      }
      $username_users = get_users($args);
      if(isset($username_users[0])) $user = $username_users[0];
      else {
        unset($args["include"]);
        $username_users = get_users($args);
        if(isset($username_users[0])) $user = $username_users[0];
        else return false;
      }
    }

    $this->make_cross_link($phpbb_user["user_id"], $user->data->ID);
  }


  private function find_phpbb_equivalent($wp_user) {

  }

  private function phpbb_create($wp_user) {

  }

  private function wp_create($phpbb_user) {
    // TO DO
  }

  private function wp_united_migration($phpbb_user) {
    if($this->hadWPunited) {
      $sql = 'SELECT user_wpuint_id
      FROM ' . USERS_TABLE . ' WHERE user_id = "' . $phpbb_user["user_id"] . '" ';

      $data = $this->phpbb->db->sql_query($sql);
      $result = $this->phpbb->db->sql_fetchrow($data);

      if(isset($result["user_wpuint_id"]) && $result["user_wpuint_id"] > 1) {
        $this->make_cross_link($phpbb_user["user_id"], $result["user_wpuint_id"]);
        return true;
      }
    }
    return false;
  }

  private function make_cross_link( $phpbb_id, $wp_id ) {
    \add_user_meta( $wp_id , "_wphpbb_forum_user_id" , $phpbb_id , true);
  }

  private function is_in_wordpress($phpbb_user) {
    $user = get_users(array('meta_key' => '_wphpbb_forum_user_id', 'meta_value' => $phpbb_user["user_id"]));
    if(isset($user[0])) return true;
    else return false;
  }

  private function is_in_phpbb($wp_user) {

  }



}


?>
