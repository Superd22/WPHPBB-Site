<?php namespace WPHPBB\controller;

class Phpbb {
  public $user;
  protected $auth;
  protected $_transitioned;
  public $db;

  function __construct() {

  }

  public function get_user() {
    return $this->user;
  }

  public function make_phpbb_env() {
    global $request;
    global $phpbb_container;
    global $phpbb_root_path, $phpEx, $user, $auth, $cache, $db, $config, $template, $table_prefix;
    global $request;
    global $phpbb_log;
    global $phpbb_dispatcher;
    global $symfony_request;
    global $phpbb_filesystem;
    if(defined('IN_PHPBB')) {
      return true;
    }
    define('IN_PHPBB', true);

    $settings = \WPHPBB\controller\Settings::get_settings();
    $phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : $settings['wordpress']['phpbb-path'];
    $phpEx = substr(strrchr(__FILE__, '.'), 1);

    $crosspatcher = new CrossPatcher($phpbb_root_path, $phpEx);
    $crosspatcher->make_phpbb_compatible();
    eval($crosspatcher->exec());

    $request->enable_super_globals();

    $this->user = $user;
    $this->auth = $auth;
    $this->db = $db;
    $this->config = $config;

    // Start session management
    $this->build_session();
  }

  private function build_session() {
    $this->user->session_begin();
    $this->auth->acl($this->user->data);
    $this->user->setup();
  }

  private function get_user_data() {
    if($this->user->data > 1) {
      return $this->user->data;
    }
    else return false;
  }

  public function get_cross_postable_forums() {
    $this->make_phpbb_env();
    if($this->get_user_data()) {
      $can_cp_to_raw = $this->auth->acl_get_list($this->user->data['user_id'], 'f_wphpbb_cross_post');
      $can_cp_to = array_keys($can_cp_to_raw);

      $sql = 'SELECT forum_id, forum_name FROM ' . FORUMS_TABLE . ' WHERE ' .
      'forum_type = ' . FORUM_POST . ' AND ' .
      $this->db->sql_in_set('forum_id', $can_cp_to);
      if ($result = $this->db->sql_query($sql)) {
        while ( $row = $this->db->sql_fetchrow($result) ) {
          $can_cs_forum_list[$row["forum_id"]] = $row["forum_name"];
        }
        $this->db->sql_freeresult($result);

        return $can_cs_forum_list;
      }
      else return null;
    }
    else return null;
  }

  public function  generate_text_for_storage(&$text, &$uid, &$bitfield, &$flags, $allow_bbcode = false, $allow_urls = false, $allow_smilies = false) {
    return \generate_text_for_storage($text, $uid, $bitfield, $flags, $allow_bbcode, $allow_urls, $allow_smilies);
  }

  public function transition_user($toID = false, $toIP = false) {
    if( ($toID === false) && ($this->_transitioned_user == true) ) {
      // Transition back to the currently logged-in user
      $this->user->data = $this->_savedData;
      $this->user->ip = $this->_savedIP;
      $this->auth = $this->_savedAuth;
      $this->this->_transitioned_user = false;
    } else if(($toID !== false) && ($toID !== $this->user->data['user_id'])) {
      // Transition to a new user
      if($this->_transitioned == false) {
        // backup current user
        $this->_savedData= $this->user->data;
        $this->_savedIP = $this->user->ip;
        $this->_savedAuth = $this->auth;
      }
      $sql = 'SELECT *
      FROM ' . USERS_TABLE . '
      WHERE user_id = ' . (int)$toID;

      $result = $this->db->sql_query($sql);
      $row = $this->db->sql_fetchrow($result);
      $this->db->sql_freeresult($result);

      $this->user->data = $row;
      $this->user->ip = $toIP;
      $this->auth->acl($this->user->data);
      $this->_transitioned_user = true;
    }
  }

  public function get_first_post_of_topic($topic_id) {
    $sql = "SELECT post_id FROM " . POSTS_TABLE . " WHERE topic_id = '{$topic_id}' ORDER BY post_id ASC LIMIT 1";

    $result = $this->db->sql_query($sql);
    $row = $this->db->sql_fetchrow($result);
    $this->db->sql_freeresult($result);

    return $row["post_id"];
  }
}

?>
