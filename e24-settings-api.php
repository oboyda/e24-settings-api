<?php
/*
 * E24 Settings API 1.0.1
 * Authored by Oleksiy Boyda
 * This code is based on Wordpress Settings API.
 * Make required changes and include this file to your theme or plugin.
 * To get a value call $e24_settings->get_option($name) or e24_get_option($name).
 */

$e24_settings = new E24_Settings_API(
	array(
		'prefix' => 'e24_',
		'menu_page' => 'options-general.php', // https://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
		'menu_title' => __('E24 Settings', 'e24'),
		'page_title' => __('E24 Settings', 'e24'),
		'btn_title' => __('Update settings', 'e24')
	),
	array(
		'general' => array(
							'title' => __('General Settings', 'e24'),
							'description' => '',
							'fields' => array(
											'option_1' => array(
												'type' => 'text',
												'title' => __('Option 1 Title', 'e24'),
//												'description' => __('This is a description which may not exist or be empty', 'e24'),
//												'default' => 'This is a default value which may not exist or be empty'
											),
											'option_2' => array(
												'type' => 'textarea',
												'title' => __('Option 2 Title', 'e24'),
//												'description' => __('This is a description which may not exist or be empty', 'e24'),
//												'default' => 'This is a default value which may not exist or be empty'
											),
											'option_3' => array(
												'type' => 'select',
												'title' => __('Option 3 Title', 'e24'),
//												'description' => __('This is a description which may not exist or be empty', 'e24'),
												'options' => array(
													'',
													'Value 1',
													'Value 2',
													'Value 3',
													'Value 4',
												),
//												'default' => 'Value 2'
											),
											'option_4' => array(
												'type' => 'checkbox',
												'title' => __('Option 4 Title', 'e24'),
//												'description' => __('This is a description which may not exist or be empty', 'e24'),
//												'default' => 1
											)
							)
		)
	)
);

function e24_get_option($name){
	global $e24_settings;
	return $e24_settings->get_option($name);
}

class E24_Settings_API {

	var $settings;
	var $sections;
	var $page_slug;
	var $settings_group;
	var $lang;

	function __construct($settings, $sections){
		$this->settings = $settings;
		$this->sections = $sections;

		$this->page_slug = $this->settings['prefix'] . 'page';
		$this->settings_group = $this->settings['prefix'] . 'settings_group';
		$this->lang = $this->define_lang();

		add_action('plugins_loaded', array($this, 'define_lang'));
		add_action('admin_menu', array($this, 'add_submenu_page'));
		add_action('admin_init', array($this, 'add_fields'));
	}

	function define_lang(){
		if(defined('ICL_LANGUAGE_CODE')){
			return '_' . ICL_LANGUAGE_CODE;
		}else{
			return '_' . substr(get_locale(), 0, 2);
		}
		return '_en';
	}

	private function get_option_full_id($id){
		return $this->settings['prefix'] . $id . $this->lang;
	}

	private function get_section_full_id($id){
		return $this->settings['prefix'] . 'section_' . $id;
	}

	function get_option($name){
		$value = get_option($this->get_option_full_id($name));
		if($value == '' || $value === false){
			foreach($this->sections as $section){
				if(isset($section['fields'][$name]['default']) && $section['fields'][$name]['default'] != ''){
					return $section['fields'][$name]['default'];
				}
			}
		}
		return $value;
	}

	function add_submenu_page(){
		add_submenu_page(
			$this->settings['menu_page'],
			$this->settings['page_title'],
			$this->settings['menu_title'],
			'manage_options',
			$this->page_slug,
			array($this, 'display_options_page')
		);
	}

	function display_options_page(){ ?>
		<div class="wrap">
			<div id="icon-options-general" class="icon32"></div>
			<h1><?php echo $this->settings['page_title']; ?></h1>
			<?php do_action($this->settings['prefix'] . 'before_form'); ?>
			<form action="options.php" method="POST">
				<?php
				do_settings_sections($this->page_slug);
				settings_fields($this->settings_group);
				submit_button($this->settings['btn_title']);
				?>
			</form>
			<?php do_action($this->settings['prefix'] . 'after_form'); ?>
		</div>
		<?php
	}

	function add_fields(){
		foreach($this->sections as $section_id => $section){
			add_settings_section(
				$this->get_section_full_id($section_id),
				$section['title'],
				array($this, 'display_section'),
				$this->page_slug
			);
			foreach($section['fields'] as $field_id => $field){
				add_settings_field(
					$this->get_option_full_id($field_id),
					$field['title'],
					array($this, 'display_field'),
					$this->page_slug,
					$this->get_section_full_id($section_id),
					array('field_id' => $field_id, 'field_config' => $field)
				);
				register_setting(
					$this->settings_group,
					$this->get_option_full_id($field_id)
				);
			}
		}
	}

	function display_section($section){
		if(isset($this->sections[$section['id']]['description']) && $this->sections[$section['id']]['description'] != ''){ ?>
			<p><?php echo $this->section[$section['id']]['description']; ?></p>
		<?php
		}
	}

	function display_field($args){
		$option_name = $this->get_option_full_id($args['field_id']);
		switch($args['field_config']['type']){
			case 'text': ?>
				<input name="<?php echo $option_name; ?>" type="text" class="regular-text" value="<?php echo $this->get_option($args['field_id']); ?>" />
				<?php break;
			case 'textarea': ?>
				<textarea name="<?php echo $option_name; ?>" class="large-text" cols="50" rows="10"><?php echo $this->get_option($args['field_id']); ?></textarea>
				<?php break;
			case 'select': ?>
				<select name="<?php echo $option_name; ?>">
					<?php foreach($args['field_config']['options'] as $option){ ?>
					<option value="<?php echo $option; ?>" <?php selected($this->get_option($args['field_id']), $option); ?>><?php echo $option; ?></option>
					<?php } ?>
				</select>
				<?php break;
			case 'checkbox': ?>
				<input name="<?php echo $option_name; ?>" type="checkbox" value="1" <?php checked($this->get_option($args['field_id']), 1); ?>/>
				<?php break;
		}
		if(isset($args['field_config']['description']) && $args['field_config']['description'] != ''){ ?>
			<p class="description"><?php echo $args['field_config']['description']; ?></p>
		<?php
		}
	}
}
