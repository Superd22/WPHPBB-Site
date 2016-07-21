<?php namespace WPHPBB\admin;

class AdminSettings {

  function __construct() {
  }

  private function create_options() {
    //add_settings_field("wphpbb_activate", "Activate WPHPBB", );
  }

  public function wphpbb_menu() {
    $settings = \WPHPBB\controller\Settings::get_settings();


    add_options_page( 'WPHPBB Settings', 'WPHPBB', 'manage_options', $settings["dev"]["menu_name"], "WPHPBB\admin\AdminSettings::main_menu_display" );
    register_setting( $settings["dev"]["option_name"], $settings["dev"]["option_name"], 'WPHPBB\admin\AdminSettings::validate_setting');
    add_settings_section($settings["dev"]["menu_main_section"], 'Main Settings', 'WPHPBB\admin\AdminSettings::main_menu_main_section', $settings["dev"]["menu_name"]);

    add_settings_field('activate', 'Activate:', 'WPHPBB\admin\AdminSettings::boolean_callback', $settings["dev"]["menu_name"], $settings["dev"]["menu_main_section"]);
  }

  public static function main_menu_main_section() {
    echo "main sessss";
  }

  public static function boolean_callback() {
    $settings = \WPHPBB\controller\Settings::get_settings();
    $options = get_option($settings["dev"]["option_name"]);
    echo "prout";
  //  echo "<input name='{$settings["dev"]["option_name"]}[activate]' type='text' value='{$options['activate']}' />";
  }

  public static function validate_setting($plugin_options) {
    return $plugin_options;
  }

  public static function main_menu_display() {
    $settings = \WPHPBB\controller\Settings::get_settings();

    echo '<div class="wrap">';
    AdminSettings::main_menu_title();
    AdminSettings::main_form_start();
    AdminSettings::main_form_end();
    echo '</div>';
  }

  private static function main_form_start() {
    $settings = \WPHPBB\controller\Settings::get_settings();
    print_r($settings["dev"]);
    echo '<form method="post" action="options.php">';
    settings_fields($settings["dev"]["option_name"]);
    do_settings_sections($settings["dev"]["menu_main_section"]);
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
