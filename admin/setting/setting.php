<?php namespace wphpbb\admin\setting;
class Setting {
  protected $page;
  protected $section;
  protected $callback;
  protected $slug;
  protected $title;
  protected $description;

  public function __construct($slug, $title, $description, $callback = null, $page = false, $section = false) {
    $settings = \WPHPBB\controller\Settings::get_settings();

    $this->slug = $slug;
    $this->title = $title;
    $this->description = $description;

    if(!$page) $this->page = $settings['dev']['menu_name'];
    else $this->page = $page;
    if(!$section) $this->section = $settings['dev']['menu_main_section'];
    else $this->section = $section;
    if(!$callback) $this->callback = array(&$this, "display_callback");
    else $this->callback = $callback;

    $this->add_setting_field();
  }

  public function display_callback() {

  }

  protected function add_setting_field() {
    \add_settings_field($this->slug, $this->title, $this->callback, $this->page, $this->section);
  }

}
?>
