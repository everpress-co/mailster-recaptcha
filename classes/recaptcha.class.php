<?php

class MailsterRecaptcha {

	private $plugin_path;
	private $plugin_url;

	public function __construct() {

		$this->plugin_path = plugin_dir_path( MAILSTER_RECAPTCHA_FILE );
		$this->plugin_url  = plugin_dir_url( MAILSTER_RECAPTCHA_FILE );

		register_activation_hook( MAILSTER_RECAPTCHA_FILE, array( &$this, 'activate' ) );
		register_deactivation_hook( MAILSTER_RECAPTCHA_FILE, array( &$this, 'deactivate' ) );

		load_plugin_textdomain( 'mailster-recaptcha' );

		add_action( 'init', array( &$this, 'init' ) );
		add_action( 'init', array( &$this, 'register_post_meta' ) );

	}

	public function activate( $network_wide ) {

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

	public function deactivate( $network_wide ) {

	}

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

	public function enqueue_block_editor_assets() {

		// only on block forms
		if ( get_post_type() !== 'mailster-form' ) {
			return;
		}

		wp_enqueue_script( 'mailster_recaptcha_script', $this->plugin_url . 'build/inspector.js', array(), MAILSTER_VERSION );
	}

	public function enqueue_script( $output, $form_id, $args = null ) {

		if ( is_array( $args ) ) {
			$enabled = get_post_meta( $form_id, 'recaptcha', true );
			if ( ! $enabled ) {
				return $output;
			}
			$forms = array( $form_id );
		} else {
			$forms = mailster_option( 'reCaptcha_forms', array() );

			if ( ! in_array( $form_id, $forms ) ) {
				return $output;
			}
		}
		wp_enqueue_script( 'mailster_recaptcha_script', $this->plugin_url . 'build/recaptcha.js', array(), MAILSTER_VERSION );

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

	public function settings_tab( $settings ) {

		$position = 4;
		$settings = array_slice( $settings, 0, $position, true ) +
					array( 'reCaptcha' => 'reCaptcha' ) +
					array_slice( $settings, $position, null, true );

		return $settings;
	}

	public function settings() {
		include $this->plugin_path . '/views/settings.php';
	}

	public function form_submission_check( $fields_errors, $entry, $request ) {

		if ( is_user_logged_in() && mailster_option( 'reCaptcha_loggedin' ) ) {
			return $fields_errors;
		}

		if ( ! empty( $fields_errors ) ) {
			return $fields_errors;
		}

		$url_params = $request->get_url_params();

		$form_id = (int) $url_params['id'];
		// legacy
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

	public function legacy_form_submission_check( $object ) {

		if ( is_user_logged_in() && mailster_option( 'reCaptcha_loggedin' ) ) {
			return $object;
		}

		$formid = isset( $_POST['formid'] ) ? intval( $_POST['formid'] ) : 1;

		if ( ! in_array( $formid, mailster_option( 'reCaptcha_forms', array() ) ) ) {
			return $object;
		}

		if ( isset( $_POST['_g-recaptcha-response'] ) ) {

			$result = $this->check_captcha( $_POST['_g-recaptcha-response'] );

			if ( is_wp_error( $result ) ) {
				$object['errors'][ $result->get_error_code() ] = $result->get_error_message();
			}
		}

		return $object;

	}



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
