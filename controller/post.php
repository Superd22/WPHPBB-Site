<?php namespace WPHPBB\controller;
class Post {
  protected $phpbb;

  function __construct() {
    $this->phpbb = new Phpbb();
  }

  public function cross_post_box() {
    \add_meta_box("wphpbb-cross-posting", "WPHPBB Cross posting", array(&$this, "cross_post_box_content"), "post", "side", "low");
  }

  public function cross_post_box_content($post) {
    echo '<input type="hidden" name="wphpbb_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

    $checked = $this->is_already_cross_posted($post->ID) ? "checked disabled" : "";

    echo "<select name='wphpbb_cross_forum' {$checked}>";
    foreach($this->get_cross_postable_forums() as $id => $option)
    echo "<option value='{$id}'>{$option}</option>";
    echo "</select>";
    echo "<br />";
    echo "<input type='checkbox' id='wphpbb_do_cross_post' name='wphpbb_do_cross_post' {$checked}> <label for='wphpbb_do_cross_post'>Cross poster l'article ?</label>";


    if($checked != "") {
      echo "<input type='hidden' name='wphpbb_do_cross_post' value='on'>";
      echo "<input type='hidden' name='wphpbb_cross_forum' value='0'>";
    }

  }

  /**
  * Cross-posts to phpbb when an article is posted
  *
  * @param int $post_id The post ID.
  * @param post $post The post object.
  * @param bool $update Whether this is an existing post being updated or not.
  */
  public function do_cross_post($post_id, $post, $update) {
    global $phpbb_root_path, $phpEx;
    $details = false;
    if($this->verif_cross_post()) {
      if($this->is_already_cross_posted($post_id)) {
        $topic_id = $this->get_cross_posted_topic_id($post_id);
        $details = $this->get_xposted_details($topic_id, true);
        $mode = "edit";
      }
      else {
        $topic_id = 0;
        $mode = "post";
      }
      // TO DO CHANGE !!
      $content = kable_filter($post->post_content);

      $user = new User($post->post_author, $this->phpbb);

      if( !$user->is_current_user() )
      $user->phpbb_switch_to();

      require_once($phpbb_root_path . 'includes/functions_posting.' . $phpEx);

      $uid = $poll = $bitfield = $options = '';
      \generate_text_for_storage($content, $uid, $bitfield, $options, true, true, true);

      $topicUsername = $this->phpbb->user->data["username"];

      $data = array(
        'forum_id' => (integer) $_REQUEST["wphpbb_cross_forum"],
        'topic_id' => $topic_id,
        'icon_id' => false,
        'enable_bbcode' => true,
        'enable_smilies' => true,
        'enable_urls' => true,
        'enable_sig' => true,
        'message' => $content,
        'message_md5' => md5($content),
        'bbcode_bitfield' => $bitfield,
        'bbcode_uid' => $uid,
        'post_edit_locked'	=> ITEM_LOCKED,
        'topic_title'		=> $post->post_title,
        'notify_set'		=> false,
        'notify'			=> false,
        'enable_indexing'	=> true,
      );

      if($details !== false) {
        if(isset($details['post_id'])) {
          $mode = 'edit';
          $forum_id = $details['forum_id'];
          $data['forum_id'] = $details['forum_id'];
          $data['topic_id'] = $details['topic_id'];
          $data['post_id'] = $details['post_id'];
          $data['poster_id'] = $details['poster_id'];
          $data['post_time'] = $details['post_time'];
          $data['topic_type'] = $details['topic_type'];
          $data['post_subject'] = $post->post_title;
          $data['post_edit_reason'] = "Mise à jour";
          $data['post_edit_user'] = $topicUsername = $this->phpbb->user->data["user_id"];
          $topicUsername = $details['topic_first_poster_name'];
        }
      }

      //  if($mode == "edit") $data["post_id"] = $this->phpbb->get_first_post_of_topic($topic_id);


      $topic_url = \submit_post($mode, $post->post_title, $topicUsername, POST_NORMAL, $poll, $data);

      if($mode === "post") add_post_meta($post_id, "_wphpbb_cross_topic_id", $data["topic_id"]);
      $user->phpbb_switch_back();
      die();
    }
  }

  public function verif_cross_post() {
    if(isset($_REQUEST["wphpbb_do_cross_post"])
    && (wp_verify_nonce( $_REQUEST['wphpbb_meta_box_nonce'] , basename(__FILE__)))
    && (array_key_exists($_REQUEST['wphpbb_cross_forum'], $this->get_cross_postable_forums()) || $_REQUEST['wphpbb_cross_forum'] === "0"))
    return true;
    else return false;
  }

  private function is_already_cross_posted($id) {
    $topic = get_post_meta($id, "_wphpbb_cross_topic_id", true);
    if($topic > 1) return true;
    else return false;
  }

  private function get_cross_posted_topic_id($id) {
    return get_post_meta($id, "_wphpbb_cross_topic_id", true);
  }

  private function get_cross_postable_forums() {
    return $this->phpbb->get_cross_postable_forums();
  }

  public function get_xposted_details($id, $is_topic_id = false) {

    if(!$is_topic_id)
      $postID = $this->get_cross_posted_topic_id($id);
    else $postID = $id;

    static $details = array();

    if(isset($details[$postID])) {
      return $details[$postID];
    }

    $details[$postID] = false;

    $sql = 'SELECT t.topic_id, p.post_id, p.post_subject, p.forum_id, p.poster_id, t.topic_time, t.topic_type, t.topic_first_poster_name, f.forum_name FROM ' . POSTS_TABLE . ' AS p, ' . TOPICS_TABLE . ' AS t, ' . FORUMS_TABLE . ' AS f WHERE
    t.topic_id = ' . (int) $postID . ' AND
    t.topic_id = p.topic_id AND (
    f.forum_id = p.forum_id OR
    p.forum_id = 0) ORDER BY post_id ASC LIMIT 1';
    if ($result = $this->phpbb->db->sql_query($sql)) {
      $row = $this->phpbb->db->sql_fetchrow($result);
      $this->phpbb->db->sql_freeresult($result);


      if(!empty($row['post_id'])) {
        if($row['topic_type'] == POST_GLOBAL) {
          $row['forum_name'] = $phpbbForum->lang['VIEW_TOPIC_GLOBAL'];
        }
        $details[$postID] = $row;
      }

    }

    return $details[$postID];
  }


}
?>
