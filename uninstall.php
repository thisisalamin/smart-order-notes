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

global $wpdb;
// No data is deleted on uninstall as per user request.
