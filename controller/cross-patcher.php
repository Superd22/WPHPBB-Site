<?php namespace wphpbb\controller;
class CrossPatcher {
  private $toExec;
  private $phpbb_root_path;
  private $phpEx;

  function __construct($_phpbb_root_path, $_phpEx) {
    $this->phpbb_root_path = $_phpbb_root_path;
    $this->phpEx = $_phpEx;
  }

  public function prepare($code) {
    $code = $this->strip_php_tags($code);
    $this->toExec .= $code."\n";
  }

  private function strip_php_tags($code) {
    $code = str_replace("<?php ", "", $code);
    $code = str_replace("?>", "", $code);

    return $code;
  }

  public function exec() {
    $code = $this->toExec;
    $this->toExec = "";

    return '?>' . $code;
  }

  public function make_phpbb_compatible() {
    $common = file_get_contents($this->phpbb_root_path . 'common.' . $this->phpEx);

    // Fix make_clickable conflict.
    $function_content = file_get_contents($this->phpbb_root_path . 'includes/functions_content.' . $this->phpEx);
    $function_content = $this->fix_function_make_clickable($function_content);

    $content = $this->replace_function_content($common, $function_content);

    $this->prepare($content);
  }

  private function fix_function_make_clickable($content) {
    return str_replace("make_clickable(", "phpbb_make_clickable(", $content);
  }

  private function replace_function_content($common, $function_content) {
    return str_replace('require($phpbb_root_path . \'includes/functions_content.\' . $phpEx);', strip_tags($function_content), $common);
  }

}
?>
