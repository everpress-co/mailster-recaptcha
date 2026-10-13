<?php
/**
 * Mailster reCaptcha integration class.
 *
 * @package Mailster_Recaptcha
 */

/**
 * Integrates Google reCAPTCHA with Mailster subscription forms.
 *
 * @since 1.0
 */
class MailsterRecaptcha {

	/**
	 * Absolute filesystem path to the plugin directory.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	private $plugin_path;

	/**
	 * URL to the plugin directory.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	private $plugin_url;

	/**
	 * Sets up paths, activation hooks, and init callbacks.
	 *
	 * @since 1.0
	 */
	public function __construct() {

		$this->plugin_path = plugin_dir_path( MAILSTER_RECAPTCHA_FILE );
		$this->plugin_url  = plugin_dir_url( MAILSTER_RECAPTCHA_FILE );

		register_activation_hook( MAILSTER_RECAPTCHA_FILE, array( &$this, 'activate' ) );
		register_deactivation_hook( MAILSTER_RECAPTCHA_FILE, array( &$this, 'deactivate' ) );

		load_plugin_textdomain( 'mailster-recaptcha' );

		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'init', array( &$this, 'register_post_meta' ) );
	}

	/**
	 * Seeds default reCAPTCHA options on plugin activation.
	 *
	 * @since 1.0
	 *
	 * @param bool $network_wide Optional. Whether the plugin is network-activated. Default false.
	 * @return void
	 */
	public function activate( $network_wide ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Hook signature.

		if ( function_exists( 'mailster' ) ) {

			$defaults = array(
				'reCaptcha_public'    => '',
				'reCaptcha_private'   => '',
				'reCaptcha_error_msg' => esc_html__( 'Captcha failed! Please reload the page and try again.', 'mailster-recaptcha' ),
				'reCaptcha_loggedin'  => false,
				'reCaptcha_forms'     => array(),
			);

			$mailster_options = mailster_options();

			foreach ( $defaults as $key => $value ) {
				if ( ! isset( $mailster_options[ $key ] ) ) {
					mailster_update_option( $key, $value );
				}
			}
		}
	}

	/**
	 * Runs on plugin deactivation.
	 *
	 * @since 1.0
	 *
	 * @param bool $network_wide Optional. Whether the plugin was network-activated. Default false.
	 * @return void
	 */
	public function deactivate( $network_wide ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.Found -- Hook signature.
	}

	/**
	 * Registers admin settings, editor assets, and form filters.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function init() {

		if ( is_admin() ) {

			add_filter( 'mailster_setting_sections', array( &$this, 'settings_tab' ) );
			add_action( 'mailster_section_tab_reCaptcha', array( &$this, 'settings' ) );

			add_action( 'enqueue_block_assets', array( &$this, 'enqueue_block_editor_assets' ) );

		}

		add_filter( 'mailster_block_form', array( &$this, 'enqueue_script' ), 10, 3 );
		add_filter( 'mailster_form', array( &$this, 'enqueue_script' ), 10, 3 );
		add_filter( 'mailster_block_form_field_errors', array( &$this, 'form_submission_check' ), 10, 3 );
		add_filter( 'mailster_submit', array( &$this, 'legacy_form_submission_check' ), 10, 3 );
	}

	/**
	 * Registers the recaptcha post meta for block forms.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function register_post_meta() {

		register_post_meta(
			'mailster-form',
			'recaptcha',
			array(
				'type'         => 'boolean',
				'show_in_rest' => true,
				'single'       => true,

			)
		);
	}

	/**
	 * Enqueues the block editor inspector script on form edit screens.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function enqueue_block_editor_assets() {

		// Only on block forms.
		if ( get_post_type() !== 'mailster-form' ) {
			return;
		}

		wp_enqueue_script( 'mailster_recaptcha_script', $this->plugin_url . 'build/inspector.js', array(), MAILSTER_VERSION, true );
	}

	/**
	 * Enqueues and localizes the front-end reCAPTCHA script for enabled forms.
	 *
	 * @since 2.0.0
	 *
	 * @param string     $output  Form HTML output.
	 * @param int        $form_id Form ID.
	 * @param array|null $args    Optional. Block form args when present; null for legacy forms. Default null.
	 * @return string Unmodified form HTML output.
	 */
	public function enqueue_script( $output, $form_id, $args = null ) {

		if ( is_array( $args ) ) {
			$enabled = get_post_meta( $form_id, 'recaptcha', true );
			if ( ! $enabled ) {
				return $output;
			}
			$forms = array( (int) $form_id );
		} else {
			$forms = array_map( 'intval', (array) mailster_option( 'reCaptcha_forms', array() ) );

			if ( ! in_array( (int) $form_id, $forms, true ) ) {
				return $output;
			}
		}
		wp_enqueue_script( 'mailster_recaptcha_script', $this->plugin_url . 'build/recaptcha.js', array(), MAILSTER_VERSION, true );

		wp_localize_script(
			'mailster_recaptcha_script',
			'mailster_recaptcha',
			array(
				'public_key' => mailster_option( 'reCaptcha_public' ),
				'forms'      => $forms,
			)
		);
		return $output;
	}

	/**
	 * Inserts the reCaptcha settings section into Mailster settings tabs.
	 *
	 * @since 1.0
	 *
	 * @param array $settings Existing settings section map.
	 * @return array Modified settings sections including reCaptcha.
	 */
	public function settings_tab( $settings ) {

		$position = 4;
		$settings = array_slice( $settings, 0, $position, true ) +
					array( 'reCaptcha' => 'reCaptcha' ) +
					array_slice( $settings, $position, null, true );

		return $settings;
	}

	/**
	 * Renders the reCaptcha settings section view.
	 *
	 * @since 1.0
	 *
	 * @return void
	 */
	public function settings() {
		include $this->plugin_path . '/views/settings.php';
	}

	/**
	 * Validates reCAPTCHA on block form submissions.
	 *
	 * @since 2.0.0
	 *
	 * @param array           $fields_errors Existing field errors.
	 * @param array           $entry         Submitted entry data.
	 * @param WP_REST_Request $request       REST request for the submission.
	 * @return array Field errors, possibly including a reCAPTCHA error.
	 */
	public function form_submission_check( $fields_errors, $entry, $request ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- Filter signature.

		if ( is_user_logged_in() && mailster_option( 'reCaptcha_loggedin' ) ) {
			return $fields_errors;
		}

		if ( ! empty( $fields_errors ) ) {
			return $fields_errors;
		}

		$url_params = $request->get_url_params();

		$form_id = (int) $url_params['id'];
		// Legacy form ID param.
		if ( ! $form_id ) {
			$form_id = $request->get_param( '_formid' );
		}

		$enabled = get_post_meta( $form_id, 'recaptcha', true );

		if ( ! $enabled ) {
			return $fields_errors;
		}

		$response = $request->get_param( '_g-recaptcha-response' );

		$result = $this->check_captcha( $response );

		if ( is_wp_error( $result ) ) {
			$fields_errors[ $result->get_error_code() ] = $result->get_error_message();
		}

		return $fields_errors;
	}

	/**
	 * Validates reCAPTCHA on legacy form submissions.
	 *
	 * @since 2.0.0
	 *
	 * @param array $submission Submission result with optional errors.
	 * @return array Modified submission result.
	 */
	public function legacy_form_submission_check( $submission ) {

		if ( is_user_logged_in() && mailster_option( 'reCaptcha_loggedin' ) ) {
			return $submission;
		}

		// phpcs:disable WordPress.Security.NonceVerification.Missing -- Nonce verified by Mailster form submit handler.
		$formid = isset( $_POST['formid'] ) ? absint( wp_unslash( $_POST['formid'] ) ) : 1;

		if ( ! in_array( $formid, array_map( 'intval', (array) mailster_option( 'reCaptcha_forms', array() ) ), true ) ) {
			return $submission;
		}

		if ( isset( $_POST['_g-recaptcha-response'] ) ) {

			$result = $this->check_captcha( sanitize_text_field( wp_unslash( $_POST['_g-recaptcha-response'] ) ) );

			if ( is_wp_error( $result ) ) {
				$submission['errors'][ $result->get_error_code() ] = $result->get_error_message();
			}
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		return $submission;
	}

	/**
	 * Verifies a reCAPTCHA response token with Google.
	 *
	 * @since 1.0
	 *
	 * @param string $response reCAPTCHA response token from the client.
	 * @return true|WP_Error True on success, WP_Error on failure.
	 */
	private function check_captcha( $response ) {

		$body = array(
			'secret'   => mailster_option( 'reCaptcha_private' ),
			'response' => $response,
		);

		$url = 'https://www.google.com/recaptcha/api/siteverify';

		$response = wp_remote_post( $url, array( 'body' => $body ) );

		if ( is_wp_error( $response ) ) {
			return new WP_Error( '_recaptcha', $response->get_error_message() );
		} else {
			$response = json_decode( wp_remote_retrieve_body( $response ) );
			if ( ! $response->success ) {
				return new WP_Error( '_recaptcha', mailster_option( 'reCaptcha_error_msg' ) );
			}
		}

		return true;
	}
}
