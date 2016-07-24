<?php namespace wphpbb\admin\setting;
class Text extends Setting {
  public function display_callback() {
    $settings = \wphpbb\controller\Settings::get_settings();
    $options = get_option($settings['dev']['option_name']);

    echo "<input name='{$settings['dev']['option_name']}[{$this->slug}]' type='text' value='{$options[$this->slug]}' />";
    echo "<label for='{$settings['dev']['option_name']}[{$this->slug}]'>{$this->description}</label>";
  }
}
?>
