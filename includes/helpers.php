<?php
/**
 * Helper functions for Password Reset Guard
 *
 * @package Password_Reset_Guard
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get the plugin version
 *
 * @return string The plugin version
 */
function prg_get_version() {
	return PRG_VERSION;
}

/**
 * Get the plugin directory path
 *
 * @return string The plugin directory path
 */
function prg_get_plugin_dir() {
	return PRG_PLUGIN_DIR;
}

/**
 * Get the plugin URL
 *
 * @return string The plugin URL
 */
function prg_get_plugin_url() {
	return PRG_PLUGIN_URL;
}

/**
 * Check if CAPTCHA is enabled
 *
 * @return bool True if CAPTCHA is enabled, false otherwise
 */
function prg_is_enabled() {
	return (bool) get_option( 'prg_enable_captcha', 1 );
}

/**
 * Get the current difficulty level
 *
 * @return string The difficulty level (easy, medium, hard)
 */
function prg_get_difficulty() {
	return get_option( 'prg_difficulty', 'medium' );
}

/**
 * Log a message for debugging (only in WP_DEBUG mode)
 *
 * @param string $message The message to log
 * @param string $level   The log level (debug, info, warning, error)
 */
function prg_log( $message, $level = 'debug' ) {
	if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
		return;
	}

	$timestamp = current_time( 'Y-m-d H:i:s' );
	$log_level = strtoupper( $level );
	$log_message = "[{$timestamp}] [{$log_level}] {$message}";

	if ( function_exists( 'error_log' ) ) {
		// phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
		error_log( $log_message );
	}
}
