<?php namespace wphpbb\controller;

class Phpbb {
  protected $user;
  protected $auth;

  function __construct() {
    global $request;
    global $phpbb_container;
    global $phpbb_root_path, $phpEx, $user, $auth, $cache, $db, $config, $template, $table_prefix;
    global $request;
    global $phpbb_dispatcher;
    global $symfony_request;
    global $phpbb_filesystem;

    define('IN_PHPBB', true);

    $phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : 'C:\Users\david\OneDrive\Documents\GitHub\SCFR\Forum\\';
    $phpEx = substr(strrchr(__FILE__, '.'), 1);

    include_once($phpbb_root_path . 'common.' . $phpEx);

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

  public function get_user_data() {
    if($this->user->data > 1) {
      return $this->user->data;
    }
    else return false;
  }

  public function get_cross_postable_forums() {
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
}

$test = new Phpbb();
?>
