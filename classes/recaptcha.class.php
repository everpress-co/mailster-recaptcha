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
	}

	public function activate( $network_wide ) {

		if ( function_exists( 'mailster' ) ) {

			$defaults = array(
				'reCaptcha_public'    => '',
				'reCaptcha_private'   => '',
				'reCaptcha_v3'        => true,
				'reCaptcha_error_msg' => esc_html__( 'Please proof that you are human!', 'mailster-recaptcha' ),
				'reCaptcha_loggedin'  => false,
				'reCaptcha_forms'     => array(),
				'reCaptcha_language'  => 'en',
				'reCaptcha_theme'     => 'light',
				'reCaptcha_size'      => 'normal',
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

		}

		add_filter( 'mailster_form_fields', array( &$this, 'form_fields' ), 10, 3 );
		add_filter( 'mailster_verify_subscriber', array( &$this, 'check_captcha' ), 10, 1 );
	}

	public function settings_tab( $settings ) {

		$position = 4;
		$settings = array_slice( $settings, 0, $position, true ) +
					array( 'reCaptcha' => 'reCaptcha™' ) +
					array_slice( $settings, $position, null, true );

		return $settings;
	}

	public function settings() {
		include $this->plugin_path . '/views/settings.php';
	}

	public function form_fields( $fields, $formid, $form ) {

		if ( is_user_logged_in() && mailster_option( 'reCaptcha_loggedin' ) && ! is_admin() ) {
			return $fields;
		}

		if ( ! in_array( $formid, mailster_option( 'reCaptcha_forms', array() ) ) ) {
			return $fields;
		}

		$position = count( $fields ) - 1;
		$fields   = array_slice( $fields, 0, $position, true ) +
					array( '_recaptcha' => $this->get_field( $form ) ) +
					array_slice( $fields, $position, null, true );

		return $fields;

	}

	public function get_field( $form ) {

		if ( mailster_option( 'reCaptcha_v3' ) ) :

			wp_enqueue_script( 'mailster_recaptcha_script', 'https://www.google.com/recaptcha/api.js?render=' . mailster_option( 'reCaptcha_public' ) . '&hl=' . mailster_option( 'reCaptcha_language' ), array(), '3.0', true );

			$identifieer = 'mailster-_recaptcha-' . $form->ID . '-' . uniqid();

			wp_add_inline_script( 'mailster_recaptcha_script', "grecaptcha.ready(function(){grecaptcha.execute('" . mailster_option( 'reCaptcha_public' ) . "', {action:'mailster_form_" . $form->ID . "_submit'}).then(function(token){var ri=document.getElementsByName('g-recaptcha-response');for (var i=0;i<ri.length;i++){ri[i].value = token;}});});" );

			$html = '<div class="mailster-wrapper mailster-_recaptcha-wrapper"><input name="g-recaptcha-response" type="hidden" id="' . $identifieer . '"></div>';
		else :
			wp_enqueue_script( 'mailster_recaptcha_script', 'https://www.google.com/recaptcha/api.js?hl=' . mailster_option( 'reCaptcha_language' ), array(), '2.0', true );
			$html = '<div class="mailster-wrapper mailster-_recaptcha-wrapper"><div class="g-recaptcha" data-sitekey="' . mailster_option( 'reCaptcha_public' ) . '" data-theme="' . mailster_option( 'reCaptcha_theme', 'light' ) . '" data-size="' . mailster_option( 'reCaptcha_size', 'normal' ) . '"></div></div>';

		endif;

		wp_print_scripts( 'mailster_recaptcha_script' );
		return $html;

	}

	public function check_captcha( $entry ) {

		if ( is_user_logged_in() && mailster_option( 'reCaptcha_loggedin' ) ) {
			return $entry;
		}

		$formid = isset( $_POST['formid'] ) ? intval( $_POST['formid'] ) : 1;

		if ( ! in_array( $formid, mailster_option( 'reCaptcha_forms', array() ) ) ) {
			return $entry;
		}

		if ( isset( $_POST['g-recaptcha-response'] ) ) {

			$body = array(
				'secret'   => mailster_option( 'reCaptcha_private' ),
				'response' => $_POST['g-recaptcha-response'],
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
		} elseif ( ! is_admin() && get_query_var( '_mailster_page' ) != 'confirm' ) {
			return new WP_Error( '_recaptcha', mailster_option( 'reCaptcha_error_msg' ) );
		}

		return $entry;

	}


}
