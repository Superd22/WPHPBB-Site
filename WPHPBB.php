<?php /*
 Plugin Name: WPHPBB
 Plugin URI: https://github.com/Superd22/WPHPBB-Site
 Description: Wordpress-PhpBB bridge
 Author: Super d
 Version: 1.0
 Author URI: https://github.com/Superd22/
 Text Domain: wphpbb
*/
  namespace wphpbb;

  require_once("controller/post.php");
  require_once("event/listener.php");

  class WPHPBB {
    function __construct() {
      $this->listener = new event\listener();
    }

  }
  $WPHPBB = new WPHPBB();
?>
