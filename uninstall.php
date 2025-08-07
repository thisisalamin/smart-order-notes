<?php
/**
 * Smart Order Notes Uninstall Script
 *
 * Fired when the plugin is uninstalled.
 * This file removes all plugin data from the database.
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Delete all template posts
$templates = get_posts(
	array(
		'post_type'      => 'sonotes_template',
		'posts_per_page' => -1,
		'post_status'    => 'any',
	)
);

foreach ( $templates as $template ) {
	wp_delete_post( $template->ID, true );
}

// Remove any orphaned meta data
global $wpdb;
$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => '_sonotes_type' ) );

// Clean up any plugin options (if we add any in the future)
delete_option( 'sonotes_version' );
delete_option( 'sonotes_settings' );

// Clear any cached data
wp_cache_flush();
