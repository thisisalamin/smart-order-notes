<?php
/**
 * Plugin Name: Smart Order Notes
 * Description: Lightweight WooCommerce extension for reusable order note templates.
 * Plugin URI: https://github.com/thisisalamin/smart-order-notes
 * Version: 1.0.0
 * Author: Crafely
 * Author URI: https://crafely.com
 * Text Domain: smart-order-notes
 * Requires at least: 5.0
 * Tested up to: 6.4
 * WC requires at least: 3.0
 * WC tested up to: 8.5
 * WC-HPOS-Compatible: yes
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Define plugin constants
define( 'SONOTES_VERSION', '1.0.0' );
define( 'SONOTES_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SONOTES_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SONOTES_PLUGIN_FILE', __FILE__ );


// Official HPOS compatibility declaration for WooCommerce (all versions)
add_action(
	'before_woocommerce_init',
	function () {
		if ( class_exists( '\\Automattic\\WooCommerce\\Utilities\\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

// Check if WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	add_action( 'admin_notices', 'sonotes_woocommerce_missing_notice' );
	return;
}

function sonotes_woocommerce_missing_notice() {
	echo '<div class="error"><p><strong>Smart Order Notes</strong> requires WooCommerce to be installed and active.</p></div>';
}

// Load modular includes
require_once SONOTES_PLUGIN_DIR . 'includes/class-sonotes-cpt.php';
require_once SONOTES_PLUGIN_DIR . 'includes/class-sonotes-order-ui.php';
require_once SONOTES_PLUGIN_DIR . 'includes/class-sonotes-metabox.php';
require_once SONOTES_PLUGIN_DIR . 'includes/class-sonotes-activation.php';
require_once SONOTES_PLUGIN_DIR . 'includes/class-sonotes-settings.php';

// Enqueue admin scripts/styles
function sonotes_admin_assets( $hook ) {
	global $typenow, $pagenow;

	// Load on WooCommerce order pages and our template pages
	if ( ( $typenow === 'shop_order' && in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) ||
		( $typenow === 'sonotes_template' ) ||
		( $hook === 'woocommerce_page_wc-orders' ) ) {

		wp_enqueue_script(
			'sonotes-admin',
			SONOTES_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			SONOTES_VERSION,
			true
		);

		wp_enqueue_style(
			'sonotes-admin',
			SONOTES_PLUGIN_URL . 'assets/css/admin.css',
			array(),
			SONOTES_VERSION
		);

		// Localize script for AJAX and translations
		wp_localize_script(
			'sonotes-admin',
			'sonotes_ajax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'sonotes_nonce' ),
				'debug'    => defined( 'WP_DEBUG' ) && WP_DEBUG,
			)
		);

		wp_localize_script(
			'sonotes-admin',
			'sonotes_i18n',
			array(
				'select_template_first' => __( 'Please select a template first.', 'smart-order-notes' ),
				'note_field_not_found'  => __( 'Could not find the order note field. Please add the note manually.', 'smart-order-notes' ),
				'inserted'              => __( 'Inserted!', 'smart-order-notes' ),
				'customer_note_desc'    => __( 'Customer notes will be sent via email to the customer.', 'smart-order-notes' ),
				'private_note_desc'     => __( 'Private notes are only visible to staff members.', 'smart-order-notes' ),
				'template_desc'         => __( 'Select a template to insert into order notes', 'smart-order-notes' ),
			)
		);
	}
}
add_action( 'admin_enqueue_scripts', 'sonotes_admin_assets' );

// Initialize plugin on activation
register_activation_hook( SONOTES_PLUGIN_FILE, 'sonotes_activate_plugin' );

function sonotes_activate_plugin() {
	// Ensure WooCommerce is active
	if ( ! class_exists( 'WooCommerce' ) ) {
		deactivate_plugins( plugin_basename( SONOTES_PLUGIN_FILE ) );
		wp_die( 'Smart Order Notes requires WooCommerce to be installed and active.' );
	}

	// Create predefined templates
	sonotes_create_predefined_templates();
}
