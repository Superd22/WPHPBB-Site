<?php namespace wphpbb\admin\setting;
class Setting {
  public function __construct($slug, $title, $callback, $page = false, $section = false) {

    if(!$page) $page = ""
  }

  protected function add_setting_field() {
    \add_settings_field($this->slug, $this->title, $this->callback, $this->page, $this->section);
  }

}
?>
