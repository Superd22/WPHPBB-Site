<?php namespace wphpbb\admin\setting;
class Checkbox extends Setting {
  public function display_callback() {
    $settings = \wphpbb\controller\Settings::get_settings();
    $options = get_option($settings['dev']['option_name']);

    $checked = $options[$this->slug] === "on" ? "checked" : "";

    echo "<input name='{$settings['dev']['option_name']}[{$this->slug}]' type='checkbox' {$checked} />";
    echo "<label for='{$settings['dev']['option_name']}[{$this->slug}]'>{$this->description}</label>";
  }
}
?>
