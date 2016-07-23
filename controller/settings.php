<?php namespace WPHPBB\controller;

class Settings implements \ArrayAccess {
  private static $_instance = null;
  private $_settings;

  private function __construct() {
    $this->build_settings();
  }

  private function build_settings() {
    $this->_settings = array(
      "dev"  => array(
        "option_name" => "wphpbb_settings",
        "menu_name" => "wphpbb-main-settings",
        "menu_main_section" => "wphpbb-main-section",
        "menu_crosspost_section" => "wphpbb-crosspost-section",
        "menu_users_section" => "wphpbb-users-section",
        "menu_migration_section" =>  "wphpbb-migration-section"
      ),
    );

    $this->_settings["wordpress"] = get_option($this->_settings['dev']['option_name']);
  }

  public function offsetSet($offset, $value) {
    if(is_null($offset))
      $this->_settings[] = $value;
    else
      $this->_settings[$offset] = $value;
  }

  public function offsetExists($offset) {
    return isset($this->_settings[$offset]);
  }

  public function offsetUnset($offset) {
    unset($this->_settings[$offset]);
  }

  public function offsetGet($offset) {
    return isset($this->_settings[$offset]) ? $this->_settings[$offset] : null;
  }

  public static function get_settings() {
    if(is_null(self::$_instance))
    self::$_instance = new Settings();

    return self::$_instance;
  }
}

?>
