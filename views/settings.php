	<table class="form-table">
		<tr valign="top">
			<th scope="row">&nbsp;</th>
			<td><p class="description"><?php printf( __( 'You have to %s to get your public and private keys', 'mailster-recaptcha' ), '<a href="https://www.google.com/recaptcha/admin" class="external">' . __( 'sign up', 'mailster-recaptcha' ) . '</a>' ); ?></p></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Site Key', 'mailster-recaptcha' ); ?></th>
			<td><p><input type="text" name="mailster_options[reCaptcha_public]" value="<?php echo esc_attr( mailster_option( 'reCaptcha_public' ) ); ?>" class="large-text"></p></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Secret Key', 'mailster-recaptcha' ); ?></th>
			<td><p><input type="text" name="mailster_options[reCaptcha_private]" value="<?php echo esc_attr( mailster_option( 'reCaptcha_private' ) ); ?>" class="large-text"></p></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'v3', 'mailster-recaptcha' ); ?></th>
			<td><label><input type="hidden" name="mailster_options[reCaptcha_v3]" value=""><input type="checkbox" name="mailster_options[reCaptcha_v3]" value="1" <?php checked( mailster_option( 'reCaptcha_v3' ) ); ?>> <?php _e( 'use version 3 of reCaptcha', 'mailster-recaptcha' ); ?></label></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Error Message', 'mailster-recaptcha' ); ?></th>
			<td><p><input type="text" name="mailster_options[reCaptcha_error_msg]" value="<?php echo esc_attr( mailster_option( 'reCaptcha_error_msg' ) ); ?>" class="large-text"></p></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Disable for logged in users', 'mailster-recaptcha' ); ?></th>
			<td><label><input type="hidden" name="mailster_options[reCaptcha_loggedin]" value=""><input type="checkbox" name="mailster_options[reCaptcha_loggedin]" value="1" <?php checked( mailster_option( 'reCaptcha_loggedin' ) ); ?>> <?php _e( 'disable the reCaptcha™ for logged in users', 'mailster-recaptcha' ); ?></label></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Forms', 'mailster-recaptcha' ); ?><p class="description"><?php _e( 'select forms which require a captcha', 'mailster-recaptcha' ); ?></p></th>
			<td>
				<ul>
				<?php
				$forms       = mailster( 'forms' )->get_all();
					$enabled = mailster_option( 'reCaptcha_forms', array() );
				foreach ( $forms as $form ) {
					$form = (object) $form;
					$id   = isset( $form->ID ) ? $form->ID : $form->id;
					echo '<li><label><input name="mailster_options[reCaptcha_forms][]" type="checkbox" value="' . $id . '" ' . ( checked( in_array( $id, $enabled ), true, false ) ) . '>' . $form->name . '</label></li>';
				}

				?>
				</ul>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Language', 'mailster-recaptcha' ); ?></th>
			<td><select name="mailster_options[reCaptcha_language]">
				<?php
				$languages   = array(
					'ar'     => 'Arabic',
					'bg'     => 'Bulgarian',
					'ca'     => 'Catalan',
					'zh-CN'  => 'Chinese (Simplified)',
					'zh-TW'  => 'Chinese (Traditional)',
					'hr'     => 'Croatian',
					'cs'     => 'Czech',
					'da'     => 'Danish',
					'nl'     => 'Dutch',
					'en-GB'  => 'English (UK)',
					'en'     => 'English (US)',
					'fil'    => 'Filipino',
					'fi'     => 'Finnish',
					'fr'     => 'French',
					'fr-CA'  => 'French (Canadian)',
					'de'     => 'German',
					'de-AT'  => 'German (Austria)',
					'de-CH'  => 'German (Switzerland)',
					'el'     => 'Greek',
					'iw'     => 'Hebrew',
					'hi'     => 'Hindi',
					'hu'     => 'Hungarain',
					'id'     => 'Indonesian',
					'it'     => 'Italian',
					'ja'     => 'Japanese',
					'ko'     => 'Korean',
					'lv'     => 'Latvian',
					'lt'     => 'Lithuanian',
					'no'     => 'Norwegian',
					'fa'     => 'Persian',
					'pl'     => 'Polish',
					'pt'     => 'Portuguese',
					'pt-BR'  => 'Portuguese (Brazil)',
					'pt-PT'  => 'Portuguese (Portugal)',
					'ro'     => 'Romanian',
					'ru'     => 'Russian',
					'sr'     => 'Serbian',
					'sk'     => 'Slovak',
					'sl'     => 'Slovenian',
					'es'     => 'Spanish',
					'es-419' => 'Spanish (Latin America)',
					'sv'     => 'Swedish',
					'th'     => 'Thai',
					'tr'     => 'Turkish',
					'uk'     => 'Uk rainian',
					'vi'     => 'Vietnamese',
				);
					$current = mailster_option( 'reCaptcha_language' );
				foreach ( $languages as $key => $name ) {
					echo '<option value="' . $key . '" ' . ( selected( $key, $current, false ) ) . '>' . $name . '</option>';
				}

				?>
			</select></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Theme', 'mailster-recaptcha' ); ?></th>
			<td><select name="mailster_options[reCaptcha_theme]">
				<?php
				$themes      = array(
					'light' => __( 'Light', 'mailster-recaptcha' ),
					'dark'  => __( 'Dark', 'mailster-recaptcha' ),
				);
					$current = mailster_option( 'reCaptcha_theme' );
				foreach ( $themes as $key => $name ) {
					echo '<option value="' . $key . '" ' . ( selected( $key, $current, false ) ) . '>' . $name . '</option>';
				}
				?>
			</select></td>
		</tr>
		<tr valign="top">
			<th scope="row"><?php _e( 'Size', 'mailster-recaptcha' ); ?></th>
			<td><select name="mailster_options[reCaptcha_size]">
				<?php
				$sizes   = array(
					'normal'  => __( 'Normal', 'mailster-recaptcha' ),
					'compact' => __( 'Compact', 'mailster-recaptcha' ),
				);
				$current = mailster_option( 'reCaptcha_size' );
				foreach ( $sizes as $key => $name ) {
					echo '<option value="' . $key . '" ' . ( selected( $key, $current, false ) ) . '>' . $name . '</option>';
				}
				?>
			</select></td>
		</tr>
	</table>
