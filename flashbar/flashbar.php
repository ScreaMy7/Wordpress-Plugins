<?php
/**
 * Plugin Name
 *
 * @wordpress-plugin
 * Plugin Name:       Flash Bar
 * Plugin URI:        https://example.com/flashbar
 * Description:       A plugin to flash sales
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Scream
 * Author URI:        https://example.com
 * Text Domain:       Flash-bar
 */

 if ( !defined('ABSPATH') ){
    echo 'Nice try ;)';
 }

 add_action('wp_body_open', 'action_wp_body_open' );
/**
 * Triggered after the opening body tag.
 *
 */
function action_wp_body_open() {
    //echo '<h3 class"flash">'. flashORnot() .'</h3>';
    if(get_option('flashbar_field')){
        echo '<div class="scrolling-limit"><div class="scrolling">'. get_option('flashbar_field') .'</div> </div>';
    } else {
        //do nothing
    }
}



// Add assets 
add_action('wp_enqueue_scripts', 'load_css');

function load_css(){
    wp_enqueue_style ( 'flashbar' , 
            plugin_dir_url(__FILE__).'css/flashbar.css',
            array(),
            null,
            'all'            
        );
}

//Flash bar Plugin page
add_action('admin_menu', 'flashbar_plugin_page');
function flashbar_plugin_page(){
    $page_title = 'Flash Bar Options';
    $menu_title = 'Flash Bar';
    $capatibily = 'manage_options';
    $slug ="flashbar-plugin";
    $callback = 'flashbar_page_html';
    $icon = "dashicons-minus";
    $position = 60;

    add_menu_page($page_title ,$menu_title, $capatibily ,$slug , $callback, $icon,$position);
}

//Registers a setting and its data.
add_action('admin_init', 'flashbar_register_settings');
function flashbar_register_settings() {
	register_setting('flashbar_option_group', 'flashbar_field');
}

function flashbar_page_html() { ?>
    <div class="wrap flashbar-wrapper">
        <form method="post" action="options.php">
            <?php settings_errors() ?>
            <?php settings_fields('flashbar_option_group'); ?>
            <label for="flashbar_field_eat">Flash Bar Text:</label>
            <input name="flashbar_field" id="flashbar_field_eat" type="text" value=" <?php echo get_option('flashbar_field'); ?> ">
            <?php submit_button(); ?>
            <h4>* To disable the Flash Bar, click on without any text.</h4>
        </form>
    </div>
    
    <?php }

add_action('admin_head', 'topbarstyle');

function topbarstyle() {
	echo '<style>
	.flashbar-wrapper {display: flex; align-items: left;margin-top:35px}
.flashbar-wrapper form {width: 100%; max-width: 800px;}
.flashbar-wrapper label {font-size: 3em; display: block; line-height:normal; margin-bottom: 30px;font-weigth:bold}
.flashbar-wrapper input {color:#666;width: 100%; padding: 30px; font-size: 3em}
.flashbar-wrapper .button {font-size: 2em; text-transform: uppercase; background: rgba(59,173,227,1);
background: linear-gradient(45deg, rgba(59,173,227,1) 0%, rgba(87,111,230,1) 25%, rgba(152,68,183,1) 51%, rgba(255,53,127,1) 100%);border:none}
  	</style>';
}