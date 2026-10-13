# Mailster reCaptcha

Contributors: everpress, mailster, xaverb  
Tags: mailster, recaptcha, captcha, newsletter, spam  
Requires at least: 6.0  
Tested up to: 7.1  
Stable tag: 2.0.2  
License: GPLv2 or later

Protect Mailster newsletter signup forms from spam with Google reCAPTCHA v3.

## Description

**Mailster reCaptcha** adds [Google reCAPTCHA](https://www.google.com/recaptcha/) spam protection to [Mailster](https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=readme&utm_term=reCaptcha) newsletter subscription forms. It uses **reCAPTCHA v3**, so visitors get a low-friction check instead of a checkbox challenge, while failed verifications are blocked on the server before a signup is accepted.

Use it when bot signups, fake subscribers, or newsletter form spam are polluting your Mailster lists.

> This plugin requires [Mailster](https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=readme&utm_term=reCaptcha) **4.0** or newer and free [Google reCAPTCHA](https://www.google.com/recaptcha/admin) site and secret keys. Form submissions send a reCAPTCHA response token to Google for verification.

### Why use Google reCAPTCHA with Mailster?

- **Stop newsletter signup spam** — Verify each protected form submission with Google before Mailster accepts the subscriber.
- **reCAPTCHA v3** — Invisible, score-based protection that runs in the background without a “I’m not a robot” checkbox.
- **Mailster block forms and legacy forms** — Enable captcha per block form in the form editor, or select legacy forms under Newsletter settings.
- **Server-side verification** — Tokens are checked with Google’s `siteverify` API so clients cannot skip the captcha by removing fields.
- **Custom error message** — Show clear feedback when verification fails.
- **Optional skip for logged-in users** — Disable reCAPTCHA for users who are already signed in to WordPress.
- **Loads only when needed** — The reCAPTCHA script is enqueued for forms that have captcha enabled, including multiple forms on one page.

### How it works

1. Install and activate **Mailster reCaptcha** alongside Mailster.
2. Create reCAPTCHA **v3** keys in the [Google reCAPTCHA admin console](https://www.google.com/recaptcha/admin).
3. Enter your **Site Key** and **Secret Key** under **Newsletter → Settings → reCaptcha**.
4. Enable captcha on the forms you want to protect (legacy form checkboxes in settings, or the **Enable reCaptcha** panel on each block form).
5. On submit, the plugin sends the token to Google and rejects the signup if verification fails.

Prefer a non-Google captcha? You may also try [Mailster Cool Captcha for Forms](https://wordpress.org/plugins/mailster-cool-captcha/). Addon overview: [Use reCaptcha with Mailster](https://mailster.co/addons/recaptcha/).

## Installation

1. Upload the entire `mailster-recaptcha` folder to the `/wp-content/plugins/` directory, or install from the WordPress plugins screen.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Make sure [Mailster](https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=readme&utm_term=reCaptcha) 4.0+ is installed and active.
4. Register your site at [google.com/recaptcha/admin](https://www.google.com/recaptcha/admin) and create **reCAPTCHA v3** keys for your domain.
5. Go to **Newsletter → Settings → reCaptcha**, enter the Site Key and Secret Key, and save.
6. Enable reCAPTCHA on the subscription forms you want to protect:
   - **Legacy forms:** select them on the same settings screen.
   - **Block forms:** edit the form and enable **Enable reCaptcha** in the form document settings.

## Frequently Asked Questions

### Does this plugin work without Mailster?

No. Mailster reCaptcha is an addon for the [Mailster Newsletter Plugin](https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=readme&utm_term=reCaptcha) and requires Mailster **4.0** or newer.

### Do I need a Google account or reCAPTCHA keys?

Yes. You need free reCAPTCHA **site** and **secret** keys from the [Google reCAPTCHA admin console](https://www.google.com/recaptcha/admin). Create keys for **reCAPTCHA v3** and add the domains where your forms are shown.

### Is this reCAPTCHA v2 or v3?

This addon uses **Google reCAPTCHA v3** (invisible / score-based). It does not show the classic checkbox widget.

### Does it work with Mailster block forms?

Yes. For block forms, open the form in the editor and enable **Enable reCaptcha** in the document settings panel. Legacy forms are enabled from **Newsletter → Settings → reCaptcha**.

### Is visitor data sent to a third party?

Yes. To verify a submission, the plugin sends the reCAPTCHA response token to Google’s verification endpoint (`www.google.com/recaptcha/api/siteverify`). Google’s reCAPTCHA service also loads in the browser for protected forms. See [Google’s reCAPTCHA documentation](https://developers.google.com/recaptcha/docs/v3) for how the service works.

### Can I turn off reCAPTCHA for logged-in users?

Yes. Enable **Disable for logged in users** on the reCAPTCHA settings screen if you do not want captcha checks for users who are already logged in to WordPress.

### Where can I get support?

For Mailster and this addon, see [mailster.co](https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=readme&utm_term=reCaptcha). For key setup or reCAPTCHA behavior, use [Google’s reCAPTCHA docs](https://developers.google.com/recaptcha/).

## Screenshots

### 1. Options interface

![Option Interface.](https://ps.w.org/mailster-recaptcha/assets/screenshot-1.png)

## Changelog

### 2.0.2

- improved: packaging and release pipeline for WordPress.org

### 2.0.1

- fixed: issue with latest block forms plugin not shoing settings page
- tested: up to WordPress 6.3

### 2.0.0

- Supports now Mailster Block Forms

### 1.6

- only output script if form has been rendered.

### 1.5

- updated file structure

### 1.4.1

- fixed: problem with confirmation if add on is enabled

### 1.4

- fixed: submitting form with with removed captcha field is no longer possible

### 1.3

- allow multiple forms on a single page (thanks to A. Grundner)
- small code improvements

### 1.2

- added: support for reCpatcha API v3 (default)

### 1.1.1

- fixed: script tag not shown on HTML forms

### 1.1

- removed option for V1
- added theme selection
- added size selection

### 1.0.1

- fixed: issue where check box doesn't get recognized

### 1.0

- initial release

## Additional Info

This plugin requires the [Mailster Newsletter Plugin for WordPress](https://mailster.co/?utm_campaign=wporg&utm_source=wordpress.org&utm_medium=readme&utm_term=reCaptcha) **4.0** or newer and [Google reCAPTCHA](https://www.google.com/recaptcha/admin) site and secret keys. Protected form submissions are verified via Google’s reCAPTCHA API.
