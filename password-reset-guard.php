<?php
/**
 * Plugin Name: Password Reset Guard
 * Description: Lightweight CAPTCHA-esque protection against password reset spam and bot attacks
 * Version: 0.1.2
 * Author: Gary Smith Marketing, LLC
 * License: GPL-2.0-or-later
 * Text Domain: password-reset-guard
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'PRG_VERSION', '0.1.2' );
define( 'PRG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PRG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Load helper functions
require_once PRG_PLUGIN_DIR . 'includes/helpers.php';

/**
 * Password Reset Guard - Main Plugin Class
 *
 * Lightweight CAPTCHA-esque protection with smart hook initialization.
 * Hooks are only loaded when needed (admin or password reset page).
 */
class Password_Reset_Guard {

	/**
	 * Constructor - Initialize only essential hooks
	 *
	 * We only register:
	 * 1. Text domain loading (needed everywhere for i18n)
	 * 2. Conditional hook loading based on page context
	 *
	 * Admin/frontend-specific hooks are loaded conditionally
	 * to avoid unnecessary processing on regular page loads.
	 */
	public function __construct() {
		// Only load admin hooks if we're in admin.
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
			add_action( 'admin_init', array( $this, 'register_settings' ) );
			return;
		}

		// Only load frontend hooks if CAPTCHA is enabled.
		if ( ! $this->is_captcha_enabled() ) {
			return;
		}

		// Hook into login_init (fires early on login page) to register password reset hooks.
		add_action( 'login_init', array( $this, 'register_password_reset_hooks' ) );
	}

	/**
	 * Register password reset hooks at the right time
	 *
	 * Called via login_init hook, which fires early on the login page.
	 * This ensures hooks are registered before the form is rendered.
	 */
	public function register_password_reset_hooks() {
		if ( $this->is_password_reset_page() ) {
			add_action( 'login_enqueue_scripts', array( $this, 'enqueue_captcha_styles' ) );
			// Use action hook, not filter - lostpassword_form is do_action, not apply_filters
			add_action( 'lostpassword_form', array( $this, 'output_captcha_field' ) );
			add_filter( 'lostpassword_post', array( $this, 'validate_captcha' ), 10, 2 );
		}
	}

	/**
	 * Add admin menu
	 */
	public function add_admin_menu() {
		add_options_page(
			__( 'Password Reset Guard', 'password-reset-guard' ),
			__( 'Password Reset Guard', 'password-reset-guard' ),
			'manage_options',
			'password-reset-guard',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Register plugin settings
	 */
	public function register_settings() {
		register_setting(
			'password_reset_guard',
			'prg_enable_captcha',
			array(
				'type'              => 'boolean',
				'sanitize_callback' => array( $this, 'sanitize_enable_captcha' ),
				'show_in_rest'      => true,
			)
		);
		register_setting(
			'password_reset_guard',
			'prg_difficulty',
			array(
				'type'              => 'string',
				'sanitize_callback' => array( $this, 'sanitize_difficulty' ),
				'show_in_rest'      => true,
			)
		);

		add_settings_section(
			'prg_main',
			__( 'CAPTCHA Settings', 'password-reset-guard' ),
			'__return_empty_string',
			'password_reset_guard'
		);

		add_settings_field(
			'prg_enable_captcha',
			__( 'Enable CAPTCHA', 'password-reset-guard' ),
			array( $this, 'render_enable_field' ),
			'password_reset_guard',
			'prg_main'
		);

		add_settings_field(
			'prg_difficulty',
			__( 'Difficulty Level', 'password-reset-guard' ),
			array( $this, 'render_difficulty_field' ),
			'password_reset_guard',
			'prg_main'
		);
	}

	/**
	 * Render enable CAPTCHA checkbox
	 */
	public function render_enable_field() {
		$value = get_option( 'prg_enable_captcha', 1 );
		?>
		<input type="checkbox" name="prg_enable_captcha" value="1" <?php checked( $value ); ?> />
		<p class="description"><?php esc_html_e( 'Check to enable CAPTCHA on password reset form', 'password-reset-guard' ); ?></p>
		<?php
	}

	/**
	 * Render difficulty level selector
	 */
	public function render_difficulty_field() {
		$value = get_option( 'prg_difficulty', 'medium' );
		?>
		<select name="prg_difficulty">
			<option value="easy" <?php selected( $value, 'easy' ); ?>><?php esc_html_e( 'Easy', 'password-reset-guard' ); ?></option>
			<option value="medium" <?php selected( $value, 'medium' ); ?>><?php esc_html_e( 'Medium', 'password-reset-guard' ); ?></option>
			<option value="hard" <?php selected( $value, 'hard' ); ?>><?php esc_html_e( 'Hard', 'password-reset-guard' ); ?></option>
		</select>
		<p class="description"><?php esc_html_e( 'Set the difficulty of the math CAPTCHA', 'password-reset-guard' ); ?></p>
		<?php
	}

	/**
	 * Sanitize enable CAPTCHA setting
	 *
	 * @param mixed $input The input value to sanitize.
	 * @return bool Sanitized boolean value.
	 */
	public function sanitize_enable_captcha( $input ) {
		return ! empty( $input ) ? 1 : 0;
	}

	/**
	 * Sanitize difficulty setting
	 *
	 * @param mixed $input The input value to sanitize.
	 * @return string Sanitized difficulty value (easy, medium, or hard).
	 */
	public function sanitize_difficulty( $input ) {
		$allowed_values = array( 'easy', 'medium', 'hard' );
		$input          = sanitize_text_field( $input );
		return in_array( $input, $allowed_values, true ) ? $input : 'medium';
	}

	/**
	 * Render settings page
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'password-reset-guard' ) );
		}
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Password Reset Guard Settings', 'password-reset-guard' ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'password_reset_guard' );
				do_settings_sections( 'password_reset_guard' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Enqueue captcha styles on login page
	 *
	 * Only called on password reset page (via conditional hook loading).
	 */
	public function enqueue_captcha_styles() {
		wp_enqueue_style(
			'prg-captcha',
			PRG_PLUGIN_URL . 'assets/css/captcha.css',
			array(),
			PRG_VERSION
		);
	}

	/**
	 * Output captcha field to password reset form
	 *
	 * Used as an action hook callback for 'lostpassword_form' action.
	 */
	public function output_captcha_field() {
		if ( ! $this->is_captcha_enabled() ) {
			return;
		}

		$captcha = $this->generate_captcha();

		?>
		<!-- Honeypot field to catch bots -->
		<p style="position: absolute; left: -9999px; top: -9999px;">
			<label for="prg_website_url"><?php esc_html_e( 'Website', 'password-reset-guard' ); ?></label>
			<input type="text" name="prg_website_url" id="prg_website_url" tabindex="-1" autocomplete="off" />
		</p>

		<p class="prg-captcha-field">
			<label for="prg_captcha"><?php esc_html_e( 'Solve the math problem:', 'password-reset-guard' ); ?></label>
			<input type="text" name="prg_captcha_answer" id="prg_captcha" class="input" placeholder="<?php esc_attr_e( 'Your answer', 'password-reset-guard' ); ?>" required />
			<input type="hidden" name="prg_captcha_num1" value="<?php echo (int) $captcha['num1']; ?>" />
			<input type="hidden" name="prg_captcha_num2" value="<?php echo (int) $captcha['num2']; ?>" />
			<input type="hidden" name="prg_captcha_operation" value="<?php echo esc_attr( $captcha['operation'] ); ?>" />
			<span class="prg-captcha-question"><?php echo (int) $captcha['num1'] . ' ' . esc_html( $captcha['operation'] ) . ' ' . (int) $captcha['num2']; ?> = ?</span>
		</p>

		<?php wp_nonce_field( 'prg_captcha_nonce', 'prg_captcha_nonce' ); ?>
		<?php
	}

	/**
	 * Validate captcha answer
	 */
	public function validate_captcha( $errors, $user ) {
		if ( ! $this->is_captcha_enabled() ) {
			return $errors;
		}

		// Verify nonce before processing any form data
		if ( ! isset( $_POST['prg_captcha_nonce'] ) ) {
			prg_log( 'CAPTCHA nonce missing from POST', 'error' );
			$errors->add( 'prg_captcha_nonce', __( 'Security verification failed. Please try again.', 'password-reset-guard' ) );
			return $errors;
		}

		$nonce = sanitize_text_field( wp_unslash( $_POST['prg_captcha_nonce'] ) );
		if ( ! wp_verify_nonce( $nonce, 'prg_captcha_nonce' ) ) {
			prg_log( 'CAPTCHA nonce verification failed', 'error' );
			$errors->add( 'prg_captcha_nonce', __( 'Security verification failed. Please try again.', 'password-reset-guard' ) );
			return $errors;
		}

		// Check honeypot field - if filled, it's a bot
		if ( isset( $_POST['prg_website_url'] ) && ! empty( $_POST['prg_website_url'] ) ) {
			prg_log( 'Honeypot field filled - likely bot attempt', 'warning' );
			$errors->add( 'prg_captcha_honeypot', __( 'CAPTCHA validation failed.', 'password-reset-guard' ) );
			return $errors;
		}

		if ( ! isset( $_POST['prg_captcha_answer'], $_POST['prg_captcha_num1'], $_POST['prg_captcha_num2'], $_POST['prg_captcha_operation'] ) ) {
			prg_log( 'CAPTCHA fields missing from POST', 'error' );
			$errors->add( 'prg_captcha_missing', __( 'CAPTCHA fields are missing.', 'password-reset-guard' ) );
			return $errors;
		}

		$num1      = (int) $_POST['prg_captcha_num1'];
		$num2      = (int) $_POST['prg_captcha_num2'];
		$operation = sanitize_text_field( wp_unslash( $_POST['prg_captcha_operation'] ) );
		$answer    = (int) $_POST['prg_captcha_answer'];

		$correct_answer = $this->calculate_answer( $num1, $num2, $operation );

		if ( $answer !== $correct_answer ) {
			prg_log( "CAPTCHA validation failed: {$num1} {$operation} {$num2} != {$answer}", 'warning' );
			$errors->add( 'prg_captcha_incorrect', __( 'CAPTCHA answer is incorrect.', 'password-reset-guard' ) );
		}

		return $errors;
	}

	/**
	 * Check if CAPTCHA is enabled
	 *
	 * @return bool True if CAPTCHA is enabled, false otherwise.
	 */
	private function is_captcha_enabled() {
		return (bool) get_option( 'prg_enable_captcha', 1 );
	}

	/**
	 * Check if we're on the password reset page
	 *
	 * Detects both the lost password form page and the reset submission handler.
	 * This prevents unnecessary hook loading on regular pages.
	 *
	 * Note: Nonce verification is NOT required here because we're only detecting
	 * the page context for conditional hook loading, not processing user input.
	 * Actual CAPTCHA validation (which requires nonce verification) happens in
	 * validate_captcha() which is only called when this method returns true.
	 *
	 * @return bool True on password reset page, false otherwise.
	 */
	private function is_password_reset_page() {
		// Check if we're on the lost password page via URL parameter.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		// No nonce needed - this is just page context detection via URL, not form processing.
		// We're not taking action on this data, just determining if we should load CAPTCHA hooks.
		if ( isset( $_GET['action'] ) && 'lostpassword' === sanitize_key( $_GET['action'] ) ) {
			return true;
		}

		// Check if we're processing a password reset form submission.
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		// No nonce needed - this is just page context detection, not form processing.
		// We're only checking for the presence of WordPress's built-in form fields
		// to detect the password reset page. Actual nonce verification happens in validate_captcha().
		if ( isset( $_POST['wp-submit'] ) && isset( $_POST['user_login'] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Generate captcha
	 */
	private function generate_captcha() {
		$difficulty = get_option( 'prg_difficulty', 'medium' );
		$operations = array( '+', '-', '*' );
		$operation  = $operations[ array_rand( $operations ) ];

		switch ( $difficulty ) {
			case 'easy':
				$num1 = wp_rand( 1, 10 );
				$num2 = wp_rand( 1, 10 );
				break;
			case 'hard':
				$num1 = wp_rand( 10, 99 );
				$num2 = wp_rand( 10, 99 );
				break;
			case 'medium':
			default:
				$num1 = wp_rand( 5, 50 );
				$num2 = wp_rand( 5, 50 );
				break;
		}

		// For subtraction, ensure num1 > num2 to avoid negative results
		if ( $operation === '-' && $num1 < $num2 ) {
			list( $num1, $num2 ) = array( $num2, $num1 );
		}

		return array(
			'num1'      => $num1,
			'num2'      => $num2,
			'operation' => $operation,
		);
	}

	/**
	 * Calculate correct answer for captcha
	 */
	private function calculate_answer( $num1, $num2, $operation ) {
		switch ( $operation ) {
			case '+':
				return $num1 + $num2;
			case '-':
				return $num1 - $num2;
			case '*':
				return $num1 * $num2;
			default:
				return 0;
		}
	}
}

// Initialize the plugin.
new Password_Reset_Guard();
