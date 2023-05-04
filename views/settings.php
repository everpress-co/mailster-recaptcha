	<table class="form-table">
		<tr valign="top">
			<th scope="row">&nbsp;</th>
			<td><p class="description"><?php printf( esc_html__( 'You have to %s to get your public and private keys', 'mailster-recaptcha' ), '<a href="https://www.google.com/recaptcha/admin" class="external">' . esc_html__( 'sign up', 'mailster-recaptcha' ) . '</a>' ); ?></p></td>
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
		<?php $block_forms = get_posts( 'post_type=newsletter_form' ); ?>
		<?php if ( ! empty( $block_forms ) ) : ?>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Block Forms', 'mailster-recaptcha' ); ?><p class="description"><?php esc_html_e( 'select forms which require a captcha', 'mailster-recaptcha' ); ?></p></th>
			<td>
				<ul>
				<?php
				$enabled = mailster_option( 'reCaptcha_block_forms', array() );
				foreach ( $block_forms as $form ) {
					echo '<li><label><input name="mailster_options[reCaptcha_block_forms][]" type="checkbox" value="' . esc_attr( $form->ID ) . '" ' . ( checked( in_array( $form->ID, $enabled ), true, false ) ) . '>' . esc_html( $form->post_title ) . '</label></li>';
				}
				?>
				</ul>
			</td>
		</tr>
		<?php endif; ?>
		<?php $forms = mailster( 'forms' )->get_all(); ?>
		<?php if ( ! empty( $forms ) ) : ?>
		<tr valign="top">
			<th scope="row"><?php esc_html_e( 'Forms', 'mailster-recaptcha' ); ?><p class="description"><?php esc_html_e( 'select forms which require a captcha', 'mailster-recaptcha' ); ?></p></th>
			<td>
				<ul>
				<?php
				$enabled = mailster_option( 'reCaptcha_forms', array() );
				foreach ( $forms as $form ) {
					$form = (object) $form;
					$id   = isset( $form->ID ) ? $form->ID : $form->id;
					echo '<li><label><input name="mailster_options[reCaptcha_forms][]" type="checkbox" value="' . esc_attr( $id ) . '" ' . ( checked( in_array( $id, $enabled ), true, false ) ) . '>' . esc_html( $form->name ) . '</label></li>';
				}
				?>
				</ul>
			</td>
		</tr>
		<?php endif; ?>
	</table>
