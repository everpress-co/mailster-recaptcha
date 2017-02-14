<?php
/*
Plugin Name: Mailster reCaptcha
Plugin URI: http://rxa.li/mailster?utm_campaign=wporg&utm_source=MailsterRrcCaptcha™+for+Forms
Description: Adds a reCaptcha™ to your Mailster Subscription forms
Version: 1.0
Author: revaxarts.com
Author URI: https://mailster.co
Text Domain: mailster-recaptcha
License: GPLv2 or later
*/


class MailsterRecaptcha {

	private $plugin_path;
	private $plugin_url;

	public function __construct() {

		$this->plugin_path = plugin_dir_path( __FILE__ );
		$this->plugin_url = plugin_dir_url( __FILE__ );

		register_activation_hook( __FILE__, array( &$this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );

		load_plugin_textdomain( 'mailster-recaptcha' );

		add_action( 'init', array( &$this, 'init' ) );
	}

	public function activate( $network_wide ) {

		if ( function_exists( 'mailster' ) ) {

			$defaults = array(
				'reCaptcha_new' => true,
				'reCaptcha_public' => '',
				'reCaptcha_private' => '',
				'reCaptcha_error_msg' => __( 'Please solve the captcha!', 'mailster-recaptcha' ),
				'reCaptcha_loggedin' => false,
				'reCaptcha_forms' => array(),
				'reCaptcha_language' => 'en',
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
		add_filter( 'mailster_submit', array( &$this, 'check_captcha' ), 10, 1 );
	}

	public function settings_tab( $settings ) {

		$position = 4;
		$settings = array_slice( $settings, 0, $position, true ) +
					array( 'reCaptcha' => 'reCaptcha™' ) +
					array_slice( $settings, $position, null, true );

		return $settings;
	}

	public function settings() {
?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">&nbsp;</th>
			<td><p class="description"><?php echo sprintf( __( 'You have to %s to get your public and private keys', 'mailster-recaptcha' ), '<a href="https://www.google.com/recaptcha/admin" class="external">' . __( 'sign up', 'mailster-recaptcha' ) . '</a>' ); ?></p></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'new reCaptcha' ,'mailster-recaptcha' ) ?></th>
			<td><label><input type="hidden" name="mailster_options[reCaptcha_new]" value=""><input type="checkbox" name="mailster_options[reCaptcha_new]" value="1" <?php checked( mailster_option( 'reCaptcha_new' ) ); ?>> <?php _e( 'use the new reCaptcha™', 'mailster-recaptcha' ) ?></label></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Site Key' ,'mailster-recaptcha' ) ?></th>
			<td><p><input type="text" name="mailster_options[reCaptcha_public]" value="<?php echo esc_attr( mailster_option( 'reCaptcha_public' ) ) ?>" class="large-text"></p></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Secret Key' ,'mailster-recaptcha' ) ?></th>
			<td><p><input type="text" name="mailster_options[reCaptcha_private]" value="<?php echo esc_attr( mailster_option( 'reCaptcha_private' ) ) ?>" class="large-text"></p></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Error Message' ,'mailster-recaptcha' ) ?></th>
			<td><p><input type="text" name="mailster_options[reCaptcha_error_msg]" value="<?php echo esc_attr( mailster_option( 'reCaptcha_error_msg' ) ) ?>" class="large-text"></p></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Disable for logged in users' ,'mailster-recaptcha' ); ?></th>
			<td><label><input type="hidden" name="mailster_options[reCaptcha_loggedin]" value=""><input type="checkbox" name="mailster_options[reCaptcha_loggedin]" value="1" <?php checked( mailster_option( 'reCaptcha_loggedin' ) ); ?>> <?php _e( 'disable the reCaptcha™ for logged in users', 'mailster-recaptcha' ) ?></label></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Forms' ,'mailster-recaptcha' ) ?><p class="description"><?php _e( 'select forms which require a captcha', 'mailster-recaptcha' ); ?></p></th>
			<td>
				<ul>
				<?php $forms = mailster( 'form' )->get_all();
					$enabled = mailster_option( 'reCaptcha_forms', array() );
				foreach ( $forms as $form ) {
					$form = (object) $form;
					$id = isset( $form->ID ) ? $form->ID : $form->id;
					echo '<li><label><input name="mailster_options[reCaptcha_forms][]" type="checkbox" value="' . $id . '" ' . (checked( in_array( $id, $enabled ), true, false )) . '>' . $form->name . '</label></li>';
				}

				?>
				</ul>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Language' ,'mailster-recaptcha' ) ?></th>
			<td><select name="mailster_options[reCaptcha_language]">
				<?php $languages = array( 'ar' => 'Arabic', 'bg' => 'Bulgarian', 'ca' => 'Catalan', 'zh-CN' => 'Chinese (Simplified)', 'zh-TW' => 'Chinese (Traditional)', 'hr' => 'Croatian', 'cs' => 'Czech', 'da' => 'Danish', 'nl' => 'Dutch', 'en-GB' => 'English (UK)', 'en' => 'English (US)', 'fil' => 'Filipino', 'fi' => 'Finnish', 'fr' => 'French', 'fr-CA' => 'French (Canadian)', 'de' => 'German', 'de-AT' => 'German (Austria)', 'de-CH' => 'German (Switzerland)', 'el' => 'Greek', 'iw' => 'Hebrew', 'hi' => 'Hindi', 'hu' => 'Hungarain', 'id' => 'Indonesian', 'it' => 'Italian', 'ja' => 'Japanese', 'ko' => 'Korean', 'lv' => 'Latvian', 'lt' => 'Lithuanian', 'no' => 'Norwegian', 'fa' => 'Persian', 'pl' => 'Polish', 'pt' => 'Portuguese', 'pt-BR' => 'Portuguese (Brazil)', 'pt-PT' => 'Portuguese (Portugal)', 'ro' => 'Romanian', 'ru' => 'Russian', 'sr' => 'Serbian', 'sk' => 'Slovak', 'sl' => 'Slovenian', 'es' => 'Spanish', 'es-419' => 'Spanish (Latin America)', 'sv' => 'Swedish', 'th' => 'Thai', 'tr' => 'Turkish', 'uk' => 'Uk rainian', 'vi' => 'Vietnamese' );
					$current = mailster_option( 'reCaptcha_language' );
				foreach ( $languages as $key => $name ) {
					echo '<option value="' . $key . '" ' . (selected( $key, $current, false )) . '>' . $name . '</option>';
				}

				?>
			</select></td>
		</tr>
	</table>

<?php
	}

	public function form_fields( $fields, $formid, $form ) {

		if ( is_user_logged_in() && mailster_option( 'reCaptcha_loggedin' ) ) {
			return $fields;
		}

		if ( ! in_array( $formid, mailster_option( 'reCaptcha_forms', array() ) ) ) {
			return $fields;
		}

		$position = count( $fields ) -1;
		$fields = array_slice( $fields, 0, $position, true ) +
					array( '_recaptcha' => $this->get_field( $form ) ) +
					array_slice( $fields, $position, null, true );

		return $fields;

	}

	public function get_field( $html ) {

		if ( mailster_option( 'reCaptcha_new' ) ) {

			wp_register_script( 'mailster_recaptcha_script', 'https://www.google.com/recaptcha/api.js?hl=' . mailster_option( 'reCaptcha_language' ) );
			wp_enqueue_script( 'mailster_recaptcha_script' );

			$html = '<div class="mailster-wrapper mailster-_recaptcha-wrapper"><div class="g-recaptcha" data-sitekey="' . mailster_option( 'reCaptcha_public' ) . '"></div></div>';

		} else {

			require_once $this->plugin_path . '/recaptcha/recaptchalib.php';
			$publickey = mailster_option( 'reCaptcha_public' );
			$html = "<script type='text/javascript'>if(!RecaptchaOptions) var RecaptchaOptions = {theme : '" . mailster_option( 'reCaptcha_theme' ) . "', lang : '" . mailster_option( 'reCaptcha_language' ) . "'}</script>";

			$html .= recaptcha_get_html( $publickey, null, is_ssl() );

		}

		return $html;

	}

	public function check_captcha( $object ) {

		if ( is_user_logged_in() && mailster_option( 'reCaptcha_loggedin' ) ) {
			return $object;
		}

		$formid = (isset( $_POST['formid'] )) ? intval( $_POST['formid'] ) : 0;

		if ( ! in_array( $formid, mailster_option( 'reCaptcha_forms', array() ) ) ) {
			return $object;
		}

		if ( isset( $_POST['g-re√captcha-response'] ) ) {

			if ( ! empty( $_POST['g-recaptcha-response'] ) ) {
				$url = add_query_arg(array(
					'secret' => mailster_option( 'reCaptcha_private' ),
					'response' => $_POST['g-recaptcha-response'],
				), 'https://www.google.com/recaptcha/api/siteverify');

				$response = wp_remote_get( $url );

				if ( is_wp_error( $response ) ) {
					$object['errors']['_recaptcha'] = $response->get_error_message();
				} else {
					$response = json_decode( wp_remote_retrieve_body( $response ) );
					if ( ! $response->success ) {
						$object['errors']['_recaptcha'] = mailster_option( 'reCaptcha_error_msg' );
					}
				}
			} else {
				$object['errors']['_recaptcha'] = mailster_option( 'reCaptcha_error_msg' );
			}
		} else {
			require_once $this->plugin_path . '/recaptcha/recaptchalib.php';
			$privatekey = mailster_option( 'reCaptcha_private' );
			$resp = recaptcha_check_answer($privatekey,
				$_SERVER['REMOTE_ADDR'],
				$_POST['recaptcha_challenge_field'],
				$_POST['recaptcha_response_field']
			);

			if ( ! $resp->is_valid ) {
				$object['errors']['_recaptcha'] = mailster_option( 'reCaptcha_error_msg' );
			}
		}

		return $object;

	}


}
new MailsterRecaptcha();
