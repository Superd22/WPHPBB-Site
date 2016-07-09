<?php namespace wphpbb\api;

require_once("../controller/phpbb.php");

class Phpbb_api extends \wphpbb\controller\Phpbb {

  function __construct() {
    parent::__construct();
    $this->get_query();
  }

  private function get_query() {
    $mode = request_var("mod", "");
    switch($mode) {
      case "CrossPostableForums":
        $this->return_func_result("get_cross_postable_forums");
      break;
    }
  }

  private function return_func_result( $func_name, $args = null ) {
    $this->return_result(call_user_func(array(&$this,$func_name), $args));
  }

  private function return_result( $data ) {
    header('Content-Type: application/json');
    print_r(json_encode($data));
  }



}

$api = new Phpbb_api();

?>
