<?php
/*
Plugin Name: Mailster reCaptcha
Plugin URI: https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=plugin&utm_term=reCaptcha
Description: Adds a reCaptcha™ to your Mailster Subscription forms
Version: 1.6
Author: EverPress
Author URI: https://mailster.co
Text Domain: mailster-recaptcha
License: GPLv2 or later
*/
define( 'MAILSTER_RECAPTCHA_VERSION', '1.6' );
define( 'MAILSTER_RECAPTCHA_REQUIRED_VERSION', '2.2' );
define( 'MAILSTER_RECAPTCHA_FILE', __FILE__ );

require_once dirname( __FILE__ ) . '/classes/recaptcha.class.php';
new MailsterRecaptcha();
