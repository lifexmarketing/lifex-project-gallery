<?php
/**
 * Plugin Name: LifeX Project Gallery
 * Plugin URI:  https://github.com/lifexmarketing/lifex-project-gallery
 * Description: A modern, accessible project gallery with a flexible shortcode and conditional asset loading.
 * Version:     2.0.0
 * Author:      LifeX Marketing
 * Author URI:  https://lifexmarketing.com
 * Text Domain: lifex-project-gallery
 * Requires at least: 6.0
 * Requires PHP: 8.0
 */

defined( 'ABSPATH' ) || exit;

define( 'LXPG_VERSION', '2.0.0' );
define( 'LXPG_DIR',     plugin_dir_path( __FILE__ ) );
define( 'LXPG_URL',     plugins_url( '/', __FILE__ ) );
define( 'LXPG_FILE',    __FILE__ );

require_once LXPG_DIR . 'includes/class-post-type.php';
require_once LXPG_DIR . 'includes/class-settings.php';
require_once LXPG_DIR . 'includes/class-assets.php';
require_once LXPG_DIR . 'includes/class-shortcode.php';
require_once LXPG_DIR . 'includes/class-single-template.php';

add_action( 'plugins_loaded', function (): void {
    new LXPG_Post_Type();
    new LXPG_Settings();
    new LXPG_Assets();
    new LXPG_Shortcode();
    new LXPG_Single_Template();
} );

register_activation_hook( __FILE__, function (): void {
    ( new LXPG_Post_Type() )->register();
    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
