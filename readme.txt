=== Password Reset Guard ===
Contributors: GSmith84
Donate link: https://github.com/gsmarketing/passwordresetguard/
Tags: captcha, password-reset, security, spam-protection, bot-protection
Requires at least: 4.9
Requires PHP: 5.6
Tested up to: 6.8
Stable tag: 0.1.2
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Lightweight CAPTCHA-esque protection against password reset spam and bot attacks.

== Description ==

Password Reset Guard is a lightweight WordPress plugin that protects your site from password reset spam attacks using a simple native math CAPTCHA like feature.

Attackers use bots to flood WordPress sites with hundreds of password reset requests, which can:
- Overload your email server
- Degrade site performance
- Create a poor user experience
- Potentially expose account information

Password Reset Guard stops these attacks with a simple human verification that:
- **Stops automated bots** - They can't solve math problems
- **Doesn't require third-party services** - No reCAPTCHA or external calls
- **Works instantly** - No delays or verification emails
- **Minimal overhead** - Pure PHP/JavaScript, no bloat

When a user visits the password reset page, they see a simple math problem (e.g., "7 + 23 = ?"). They must solve it correctly to proceed. That's it. Simple. Effective. Lightweight.

= Features =

- **Lightweight** - Less than 10 KB of code, zero dependencies
- **Native Math CAPTCHA** - Simple addition, subtraction, and multiplication
- **Easy to Use** - Just solve a quick math problem
- **Configurable** - Three difficulty levels (Easy, Medium, Hard)
- **No Database** - Uses WordPress options, no extra tables
- **Accessible** - Works with screen readers and keyboard navigation
- **Mobile Friendly** - Responsive design on all devices
- **Privacy Focused** - No third-party services or tracking

= Installation =

1. Download the plugin from GitHub or WordPress.org
2. Go to WordPress Admin → Plugins → Add New
3. Click "Upload Plugin" and select the ZIP file
4. Click "Install Now" and then "Activate"
5. Go to Settings → Password Reset Guard to configure

Or manually:
1. Extract the ZIP file to `/wp-content/plugins/password-reset-guard/`
2. Go to Plugins and click "Activate"
3. Go to Settings → Password Reset Guard to configure

= Configuration =

After activation, go to **Settings → Password Reset Guard**:

- **Enable CAPTCHA** - Toggle CAPTCHA protection on/off
- **Difficulty Level**:
  - Easy: Numbers 1-10 (accessibility-friendly)
  - Medium: Numbers 5-50 (recommended, balanced)
  - Hard: Numbers 10-99 (maximum protection)

= Requirements =

- WordPress 4.9 or higher
- PHP 5.6 or higher
- No additional plugins or services required

= Security =

- CAPTCHA answers are validated on the server (authoritative)
- All form inputs are sanitized and validated
- Output is properly escaped
- No sensitive data is stored
- Follows WordPress security best practices

= Frequently Asked Questions =

= Why not use reCAPTCHA? =

reCAPTCHA requires:
- Third-party service calls (slower)
- Google account tracking
- Additional dependencies
- Complex configuration

Password Reset Guard is simpler, faster, and doesn't depend on external services.

= Will this block legitimate users? =

No. Math problems are easy for humans but impossible for bots. Users can solve them instantly.

= Can I disable it? =

Yes. Go to Settings → Password Reset Guard and toggle "Enable CAPTCHA" off.

= What if I want to change the difficulty? =

Go to Settings → Password Reset Guard and select your preferred difficulty level.

= Does this work with custom password reset plugins? =

Password Reset Guard hooks into WordPress's standard password reset process. Custom plugins that bypass these hooks won't be affected. Most popular password reset plugins work fine.

= Will this slow down my site? =

No. The plugin is lightweight (~10 KB) and only loads on the password reset page when CAPTCHA is enabled. Regular page loads have minimal overhead.

= Does this collect user data? =

No. Password Reset Guard doesn't collect, store, or transmit any user data. It's privacy-focused by design.

= How do I report a bug? =

Visit the GitHub repository and open an issue with:
- Your WordPress version
- Your PHP version
- Steps to reproduce the issue

= Changelog =

= 0.1.2 =
* Security: Implement proper nonce sanitization with wp_unslash() before wp_verify_nonce()
* Code quality: Add justification comments for intentional phpcs ignores
* Improve: Document page detection logic that doesn't require nonce verification
* Note: Page context detection in is_password_reset_page() only checks for WordPress form field presence, actual security validation happens in validate_captcha()

= 0.1.1 =
* Security: Add nonce verification to CAPTCHA form (WordPress security best practices)
* Security: Implement proper input sanitization with wp_unslash() before sanitization
* Security: Remove deprecated load_plugin_textdomain() (automatic on WordPress.org)
* Fix: Add sanitization callbacks to register_setting() for all plugin options
* Compatibility: Update "Tested up to" to WordPress 6.8

= 0.1.0 =
* Initial release
* Math CAPTCHA with three difficulty levels (Easy, Medium, Hard)
* Admin settings page for configuration
* Mobile responsive design with accessibility features
* Full internationalization support for translations
* Server-side CAPTCHA validation
* Client-side form validation with error feedback
* Zero dependencies, no external services
* GPL-2.0-or-later license

== Screenshots ==

1. Password reset form with CAPTCHA protection
2. Admin settings page with difficulty selector
3. CAPTCHA field on mobile devices

== Support ==

For support, please:
1. Check the Frequently Asked Questions above
2. Visit the GitHub repository for issues and discussions
3. Include your WordPress version and PHP version when reporting issues

== License ==

This plugin is licensed under the GPL-2.0-or-later. See LICENSE file for details.

The GPL comes with ABSOLUTELY NO WARRANTY. This is free software, and you are welcome to redistribute it under certain conditions. See https://www.gnu.org/licenses/gpl-2.0.html for more details.
