<?php
/*
Plugin Name: Mailster reCaptcha
Requires Plugins: mailster
Plugin URI: https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=plugin&utm_term=reCaptcha
Description: Adds a reCaptcha™ to your Mailster Subscription forms
Version: 2.0.1
Author: EverPress
Author URI: https://mailster.co
Text Domain: mailster-recaptcha
License: GPLv2 or later
*/
define( 'MAILSTER_RECAPTCHA_VERSION', '2.0.1' );
define( 'MAILSTER_RECAPTCHA_REQUIRED_VERSION', '4.0' );
define( 'MAILSTER_RECAPTCHA_FILE', __FILE__ );

require_once __DIR__ . '/classes/recaptcha.class.php';
new MailsterRecaptcha();
