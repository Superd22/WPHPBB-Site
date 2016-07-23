<?php namespace WPHPBB\admin;

class AdminSettings {

  function __construct() {
  }

  private function create_options() {
    //add_settings_field("wphpbb_activate", "Activate WPHPBB", );
  }

  public function wphpbb_menu() {
    $settings = \WPHPBB\controller\Settings::get_settings();

    add_options_page( 'WPHPBB Settings', 'WPHPBB', 'manage_options', $settings['dev']['menu_name'], "WPHPBB\admin\AdminSettings::main_menu_display" );
    register_setting( $settings['dev']['option_name'], $settings['dev']['option_name'], 'WPHPBB\admin\AdminSettings::validate_setting');

    $this->wphpbb_sections();
    $this->wphpbb_settings();

    //add_settings_field('activate', 'Activate:', 'WPHPBB\admin\AdminSettings::boolean_callback', $settings['dev']['menu_name'], $settings['dev']['menu_main_section']);
  }

  private function wphpbb_sections() {
    $settings = \WPHPBB\controller\Settings::get_settings();
    add_settings_section( $settings['dev']['menu_main_section'], 'Main Settings', 'WPHPBB\admin\AdminSettings::main_menu_main_section', $settings['dev']['menu_name']);
    if($this->should_display_more_sections()) {
      add_settings_section( $settings['dev']['menu_crosspost_section'], 'Cross-post settings', 'WPHPBB\admin\AdminSettings::main_crosspost_section', $settings['dev']['menu_name']);
      add_settings_section( $settings['dev']['menu_users_section'], 'Users integration settings', 'WPHPBB\admin\AdminSettings::main_users_section', $settings['dev']['menu_name']);
    }
  }

  private function should_display_more_sections() {
    $settings = \WPHPBB\controller\Settings::get_settings();
    return ($settings["wordpress"]["activate"] == "on" && $settings["wordpress"]["phpbb-path"] !== '');
  }

  private function wphpbb_settings() {
    $settings = \WPHPBB\controller\Settings::get_settings();
    $activate = new setting\Checkbox('activate', 'Activate WPHPBB :', 'Turn the plugin on or off');
    $path     = new setting\Text('phpbb-path', 'PHPBB Path :', ' the <u>ABSOLUTE</u> path to phpbb');
    if($this->should_display_more_sections()) {
      // Cross-posting
      $d = new setting\Checkbox('wphpbb-crosspost', 'Activate crossposting :', 'Turn cross-posting on or off', null, false, $settings['dev']['menu_crosspost_section']);
      // To do : template;
    }
  }

  public static function main_menu_main_section() {
    echo "Activate or de-activate WPHPBB and set the main forum path";
  }

  public static function main_crosspost_section() {
    echo "Settings for cross-posting wordpress post to phpbb topics";
  }

  public static function main_users_section() {
    echo "Settings for user integration";
  }

  public static function validate_setting($plugin_options) {
    return $plugin_options;
  }

  public static function main_menu_display() {
    echo '<div class="wrap">';
    AdminSettings::main_menu_title();
    AdminSettings::main_form_start();
    AdminSettings::main_form_end();
    echo '</div>';
  }

  private static function main_form_start() {
    $settings = \WPHPBB\controller\Settings::get_settings();
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
