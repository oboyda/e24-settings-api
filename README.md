# e24-settings-api

The purpose of this code snippet is to make the process of adding options to your Wordpress plugins and themes easier while still being based on the Settings API. In order to use it include e24-settings-api.php file into your theme's functions.php or into the main plugin's file. The settigns are simple, you pass them when instantiating the E24_Settings_API class which acceps two arguments:

$settings
---------

    $settings['prefix'] (string)(required):
      A unique prefix added to the option names and in some other places

    $settings['menu_page'] (string)(required):
      The parent menu page under which the current options page will be added. 
      See possible options here https://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters

    $settings['menu_title'] (string)(required):
      The text to be used for the menu link.

    $settings['page_title'] (string)(required):
      The text to be displayed in the title tags of the page when the menu is selected.

    $settings['btn_title'] (string)(required):
      The title to be displayed in the submit button.

$sections
---------

    $sections['section_id']['title'] (string)(required):
      The name of the section.

    $sections['section_id']['description'] (string)(optional):
      The description of the section. Can be empty or not set at all.

    $sections['section_id']['fields']['field_id']['type'] (string)(required):
      The type of the input. Available values are: 'text', 'textarea', 'select', 'checkbox'.

    $sections['section_id']['fields']['field_id']['title'] (string)(required):
      The title of the input to be used in the label tag.

    $sections['section_id']['fields']['field_id']['description'] (string)(optional):
      The description of the input. Can be empty or not set at all.

    $sections['section_id']['fields']['field_id']['options'] (array)(required):
      Available options. Only required when input type is set to select.

    $sections['section_id']['fields']['field_id']['default'] (optional):
      The default value. Will be used if the option is empty.

To get a value call $e24_settings->get_option($name) or e24_get_option($name).
