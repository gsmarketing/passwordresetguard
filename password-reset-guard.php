<?php
/**
 * Plugin Name: Password Reset Guard
 * Description: Lightweight CAPTCHA protection against password reset spam and bot attacks
 * Version: 1.0.0
 * Author: Gary Smith Marketing, LLC
 * License: GPL-2.0-or-later
 * Text Domain: password-reset-guard
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'PRG_VERSION', '1.0.0' );
define( 'PRG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PRG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Password Reset Guard - Main Plugin Class
 *
 * Lightweight CAPTCHA protection with smart hook initialization.
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
		// Load text domain early for i18n support.
		add_action( 'init', array( $this, 'load_textdomain' ) );

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

		// Only load password reset hooks on password reset pages.
		if ( $this->is_password_reset_page() ) {
			add_action( 'login_enqueue_scripts', array( $this, 'enqueue_captcha_styles' ) );
			add_filter( 'lostpassword_form', array( $this, 'add_captcha_field' ) );
			add_filter( 'lostpassword_post', array( $this, 'validate_captcha' ), 10, 2 );
		}
	}

	/**
	 * Load plugin text domain
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'password-reset-guard',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages'
		);
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
		register_setting( 'password_reset_guard', 'prg_enable_captcha' );
		register_setting( 'password_reset_guard', 'prg_difficulty' );

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
	 * Add captcha field to password reset form
	 */
	public function add_captcha_field( $html ) {
		if ( ! $this->is_captcha_enabled() ) {
			return $html;
		}

		$captcha = $this->generate_captcha();

		$captcha_html = sprintf(
			'<p class="prg-captcha-field">
				<label for="prg_captcha">%s</label>
				<input type="text" name="prg_captcha_answer" id="prg_captcha" class="input" placeholder="%s" required />
				<input type="hidden" name="prg_captcha_num1" value="%d" />
				<input type="hidden" name="prg_captcha_num2" value="%d" />
				<input type="hidden" name="prg_captcha_operation" value="%s" />
				<span class="prg-captcha-question">%d %s %d = ?</span>
			</p>',
			esc_html__( 'Solve the math problem:', 'password-reset-guard' ),
			esc_attr__( 'Your answer', 'password-reset-guard' ),
			(int) $captcha['num1'],
			(int) $captcha['num2'],
			esc_attr( $captcha['operation'] ),
			(int) $captcha['num1'],
			esc_html( $captcha['operation'] ),
			(int) $captcha['num2']
		);

		return $html . $captcha_html;
	}

	/**
	 * Validate captcha answer
	 */
	public function validate_captcha( $errors, $user ) {
		if ( ! $this->is_captcha_enabled() ) {
			return $errors;
		}

		if ( ! isset( $_POST['prg_captcha_answer'], $_POST['prg_captcha_num1'], $_POST['prg_captcha_num2'], $_POST['prg_captcha_operation'] ) ) {
			$errors->add( 'prg_captcha_missing', __( 'CAPTCHA fields are missing.', 'password-reset-guard' ) );
			return $errors;
		}

		$num1      = (int) $_POST['prg_captcha_num1'];
		$num2      = (int) $_POST['prg_captcha_num2'];
		$operation = sanitize_text_field( $_POST['prg_captcha_operation'] );
		$answer    = (int) $_POST['prg_captcha_answer'];

		$correct_answer = $this->calculate_answer( $num1, $num2, $operation );

		if ( $answer !== $correct_answer ) {
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
	 * @return bool True if on password reset page, false otherwise.
	 */
	private function is_password_reset_page() {
		// Check if we're on the lost password page.
		if ( isset( $_GET['action'] ) && 'lostpassword' === $_GET['action'] ) {
			return true;
		}

		// Check if we're processing a password reset form submission.
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
