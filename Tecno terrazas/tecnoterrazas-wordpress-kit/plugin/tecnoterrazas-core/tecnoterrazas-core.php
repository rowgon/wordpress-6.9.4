<?php
/**
 * Plugin Name: Tecnoterrazas Core
 * Description: Base funcional de Tecnoterrazas para CPT, ACF, shortcodes, AJAX y estilos frontend.
 * Version: 0.1.0
 * Author: Codex
 * Text Domain: tecnoterrazas-core
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TT_CORE_VERSION', '0.1.0' );
define( 'TT_CORE_FILE', __FILE__ );
define( 'TT_CORE_PATH', plugin_dir_path( __FILE__ ) );
define( 'TT_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once TT_CORE_PATH . 'includes/class-tt-core.php';

\TT\Core::boot();
