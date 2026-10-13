<?php
/**
 * ReCaptcha settings section markup.
 *
 * @package Mailster_Recaptcha
 */

?>
	<table class="form-table">
		<tr valign="top">
			<th scope="row">&nbsp;</th>
			<td><p class="description">
			<?php
			/* translators: %s: link to Google reCAPTCHA admin signup */
			printf( esc_html__( 'You have to %s to get your public and private keys', 'mailster-recaptcha' ), '<a href="https://www.google.com/recaptcha/admin" class="external">' . esc_html__( 'sign up', 'mailster-recaptcha' ) . '</a>' );
			?>
			</p></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Site Key', 'mailster-recaptcha' ); ?></th>
			<td><p><input type="password" name="mailster_options[reCaptcha_public]" value="<?php echo esc_attr( mailster_option( 'reCaptcha_public' ) ); ?>" class="large-text"></p></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Secret Key', 'mailster-recaptcha' ); ?></th>
			<td><p><input type="password" name="mailster_options[reCaptcha_private]" value="<?php echo esc_attr( mailster_option( 'reCaptcha_private' ) ); ?>" class="large-text"></p></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Error Message', 'mailster-recaptcha' ); ?></th>
			<td><p><input type="text" name="mailster_options[reCaptcha_error_msg]" value="<?php echo esc_attr( mailster_option( 'reCaptcha_error_msg' ) ); ?>" class="large-text"></p></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Disable for logged in users', 'mailster-recaptcha' ); ?></th>
			<td><label><input type="hidden" name="mailster_options[reCaptcha_loggedin]" value=""><input type="checkbox" name="mailster_options[reCaptcha_loggedin]" value="1" <?php checked( mailster_option( 'reCaptcha_loggedin' ) ); ?>> <?php esc_html_e( 'disable the reCaptcha for logged in users', 'mailster-recaptcha' ); ?></label></td>
		</tr>

		<?php $mailster_recaptcha_forms = mailster( 'forms' )->get_all(); ?>
		<?php if ( ! empty( $mailster_recaptcha_forms ) ) : ?>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Forms', 'mailster-recaptcha' ); ?><p class="description"><?php esc_html_e( 'select forms which require a captcha', 'mailster-recaptcha' ); ?></p></th>
			<td>
				<ul>
				<?php
				$mailster_recaptcha_enabled = array_map( 'intval', (array) mailster_option( 'reCaptcha_forms', array() ) );
				foreach ( $mailster_recaptcha_forms as $mailster_recaptcha_form ) {
					$mailster_recaptcha_form    = (object) $mailster_recaptcha_form;
					$mailster_recaptcha_form_id = isset( $mailster_recaptcha_form->ID ) ? (int) $mailster_recaptcha_form->ID : (int) $mailster_recaptcha_form->id;
					echo '<li><label><input name="mailster_options[reCaptcha_forms][]" type="checkbox" value="' . esc_attr( (string) $mailster_recaptcha_form_id ) . '" ' . ( checked( in_array( $mailster_recaptcha_form_id, $mailster_recaptcha_enabled, true ), true, false ) ) . '>' . esc_html( $mailster_recaptcha_form->name ) . '</label></li>';
				}
				?>
				</ul>
			</td>
		</tr>
		<?php endif; ?>
	</table>
