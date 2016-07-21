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
  require_once("controller/cross-patcher.php");
  require_once("controller/phpbb.php");
  require_once("controller/user.php");
  require_once("controller/user_integrator.php");
  require_once("controller/settings.php");
  require_once("admin/admin-settings.php");
  require_once("event/listener.php");
  require_once("migrations/wpunited/post_integrator.php");

  error_reporting(-1);
  //$t = new migrations\wpunited\Post_integrator();
  //$t->do_integration();

  class WPHPBB {
    function __construct() {
      $this->listener = new event\listener();
    }

  }
  $WPHPBB = new WPHPBB();
?>
