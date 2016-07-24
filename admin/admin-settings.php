<?php namespace wphpbb\admin;

class AdminSettings {

  function __construct() {
  }

  private function create_options() {
    //add_settings_field("wphpbb_activate", "Activate WPHPBB", );
  }

  public function wphpbb_menu() {
    $settings = wphpbb\controller\Settings::get_settings();

    add_options_page( 'WPHPBB Settings', 'WPHPBB', 'manage_options', $settings['dev']['menu_name'], "WPHPBB\admin\AdminSettings::main_menu_display" );
    register_setting( $settings['dev']['option_name'], $settings['dev']['option_name'], array(&$this, 'validate_setting'));

    $this->wphpbb_sections();
    $this->wphpbb_settings();

    //add_settings_field('activate', 'Activate:', 'WPHPBB\admin\AdminSettings::boolean_callback', $settings['dev']['menu_name'], $settings['dev']['menu_main_section']);
  }

  private function wphpbb_sections() {
    $settings = \wphpbb\controller\Settings::get_settings();
    add_settings_section( $settings['dev']['menu_main_section'], 'Main Settings', 'WPHPBB\admin\AdminSettings::main_menu_main_section', $settings['dev']['menu_name']);
    if($this->should_display_more_sections()) {
      add_settings_section( $settings['dev']['menu_crosspost_section'], 'Cross-post settings', 'WPHPBB\admin\AdminSettings::main_crosspost_section', $settings['dev']['menu_name']);
      //add_settings_section( $settings['dev']['menu_users_section'], 'Users integration settings', 'WPHPBB\admin\AdminSettings::main_users_section', $settings['dev']['menu_name']);
      add_settings_section( $settings['dev']['menu_migration_section'], 'Migration settings', 'WPHPBB\admin\AdminSettings::main_migration_section', $settings['dev']['menu_name']);
    }
  }

  private function should_display_more_sections() {
    $settings = \wphpbb\controller\Settings::get_settings();
    return ($settings["wordpress"]["activate"] == "on" && $settings["wordpress"]["phpbb-path"] !== '');
  }

  private function wphpbb_settings() {
    $settings = \wphpbb\controller\Settings::get_settings();
    $activate = new setting\Checkbox('activate', 'Activate WPHPBB :', 'Turn the plugin on or off');
    $path     = new setting\Text('phpbb-path', 'PHPBB Path :', ' the <u>ABSOLUTE</u> path to phpbb');
    if($this->should_display_more_sections()) {
      // Cross-posting
      $d = new setting\Checkbox('wphpbb-crosspost', 'Activate crossposting :', 'Turn cross-posting on or off', null, false, $settings['dev']['menu_crosspost_section']);

      // Migration
      new setting\Checkbox('wphpbb-do-user-migration', 'Do user migration :', 'Try and create WP-users for PHPBB-users and vice-verca, as well as import WP_United users.', null, false, $settings['dev']['menu_migration_section']);
      new setting\Checkbox('wphpbb-do-posts-migration', 'Do cross-posts migration :', '[WPUNITED ONLY] Fetches all old "WPUNITED" cross-posts into WPHPBB', null, false, $settings['dev']['menu_migration_section']);
    }
  }

  public static function main_menu_main_section() {
    echo "Activate or de-activate WPHPBB and set the main forum path";
  }

  public static function main_crosspost_section() {
    echo "Settings for cross-posting wordpress post to phpbb topics";
  }

  public static function main_migration_section() {
    echo "Perform migration operations";
  }

  public static function main_users_section() {
    echo "Settings for user integration";
  }

  public function validate_setting($settings) {
    if(isset($settings["activate"]) && $settings["activate"] === "on") {
      $this->check_path($settings);
    }

    return $settings;
  }

  private static function check_path(&$settings) {
    if(AdminSettings::is_setting_equal($settings["_is_connected"], true) && isset($settings["_phpbb_old_path"]) && isset($settings["phpbb-path"])) {
      if($settings["_phpbb_old_path"] === $settings["phpbb-path"]) return true;
      else $settings = AdminSettings::do_path($settings);
    }
    else $settings = AdminSettings::do_path($settings);
  }

  public static function do_path($settings) {
    return $settings;
  }
  public static function is_setting_equal($setting, $expected, $strict = false) {
    if($strict) return (isset($setting) && $setting === $expected);
    else return (isset($setting) && $setting == $expected);
  }

  public static function main_menu_display() {
    echo '<div class="wrap">';
    AdminSettings::main_menu_title();
    AdminSettings::main_form_start();
    AdminSettings::main_form_end();
    echo '</div>';
  }

  private static function main_form_start() {
    $settings = \wphpbb\controller\Settings::get_settings();
    echo '<form method="post" action="options.php">';
    \settings_fields($settings['dev']['option_name']);
    \do_settings_sections($settings['dev']['menu_name']);
  }

  private static function main_form_end() {
    echo '<p class="submit"> <input name="Submit" type="submit" class="button-primary" value="'.esc_attr__('Save Changes').'" />';
    echo "</form>";
  }

  public static function main_menu_title() {
    echo "<h1>WPHPBB Settings</h1>";
  }

}
?>
