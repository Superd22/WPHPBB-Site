<?php /*
 Plugin Name: WPHPBB
 Plugin URI: https://github.com/Superd22/WPHPBB-Site
 Description: Wordpress-PhpBB bridge
 Author: Super d
 Version: 1.0
 Author URI: https://github.com/Superd22/
 Text Domain: WPHPBB
*/
  namespace WPHPBB;

  require_once("controller/post.php");
  require_once("controller/cross-patcher.php");
  require_once("controller/phpbb.php");
  require_once("controller/user.php");
  require_once("controller/settings.php");
  require_once("admin/admin-settings.php");
  require_once("admin/setting/setting.php");
  require_once("admin/setting/text.php");
  require_once("admin/setting/checkbox.php");
  require_once("admin/setting/phpbb-path.php");
  require_once("event/listener.php");
  require_once("migrations/wpunited/post_integrator.php");
  require_once("migrations/user_integrator.php");
  require_once("helper/Post.php");
  
  class WPHPBB {
    function __construct() {
      $this->listener = new event\listener();
    }

  }
  $WPHPBB = new WPHPBB();
?>
