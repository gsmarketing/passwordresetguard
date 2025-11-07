# Development Environment Setup

This guide helps you set up a local development environment for Password Reset Guard.

## Prerequisites

- PHP 5.6+ (or 7.4+ recommended)
- WordPress 4.9+ (latest recommended for testing)
- Git
- A code editor (VS Code, PHPStorm, etc.)
- Local WordPress installation (using Local, XAMPP, Docker, etc.)

## Setting Up Your Development Environment

### 1. Choose a Local WordPress Setup

Pick one of these popular options:

#### Option A: Local (Recommended)
- Download: https://localwp.com/
- Easy WordPress site creation in one click
- Built-in PHP and database
- Recommended for beginners

#### Option B: XAMPP
- Download: https://www.apachefriends.org/
- Manual WordPress installation required
- Lightweight, requires more setup knowledge

#### Option C: Docker
- Download: https://www.docker.com/
- Most powerful option
- Requires Docker knowledge

### 2. Clone the Repository

```bash
cd /path/to/your/wordpress/wp-content/plugins/
git clone https://github.com/yourusername/password-reset-guard.git
cd password-reset-guard
```

### 3. Activate the Plugin

1. Log into your local WordPress admin
2. Go to **Plugins**
3. Find "Password Reset Guard"
4. Click **Activate**

## Development Tools

### PHP Code Sniffer (Optional but Recommended)

Install for code quality checks:

```bash
composer require --dev squizlabs/php_codesniffer wpcsstandards/wpcs
```

Run PHPCS:

```bash
./vendor/bin/phpcs password-reset-guard.php --standard=phpcs.xml.dist
```

### WordPress Coding Standards

```bash
composer require --dev wp-coding-standards/wpcs
composer require --dev squizlabs/php_codesniffer
```

## Project Structure

```
password-reset-guard/
├── password-reset-guard.php       # Main plugin file (entry point)
├── assets/
│   ├── css/captcha.css            # CAPTCHA styling
│   └── js/captcha.js              # Client-side validation
├── includes/
│   └── helpers.php                # Helper functions
├── languages/
│   └── password-reset-guard.pot   # Translation template
├── .github/                       # GitHub templates
├── README.md                      # User documentation
├── INSTALL.md                     # Installation guide
├── CONTRIBUTING.md                # Contribution guidelines
├── CHANGELOG.md                   # Version history
└── LICENSE                        # GPL-2.0 license
```

## Key Files to Know

### Main Plugin File: `password-reset-guard.php`

This is the entry point. It:
- Defines plugin constants
- Initializes the `Password_Reset_Guard` class
- Hooks into WordPress actions/filters

### CAPTCHA Logic: Password_Reset_Guard class

Main methods:
- `generate_captcha()` - Creates random math problem
- `calculate_answer()` - Computes correct answer
- `add_captcha_field()` - Injects field into form
- `validate_captcha()` - Validates user answer

### Frontend: `assets/js/captcha.js`

- Form validation
- Error handling
- User feedback

### Styling: `assets/css/captcha.css`

- CAPTCHA field styling
- Responsive design
- Accessibility features

## Common Development Tasks

### Adding a New Setting

1. Register the setting in `register_settings()`:
   ```php
   register_setting( 'password_reset_guard', 'prg_my_new_setting' );
   ```

2. Add settings field:
   ```php
   add_settings_field(
       'prg_my_new_setting',
       __( 'My New Setting', 'password-reset-guard' ),
       array( $this, 'render_my_field' ),
       'password_reset_guard',
       'prg_main'
   );
   ```

3. Retrieve in code:
   ```php
   $value = get_option( 'prg_my_new_setting' );
   ```

### Adding Translations

1. Update strings in POT file: `languages/password-reset-guard.pot`
2. Create language file: `languages/password-reset-guard-LOCALE.po`
3. Compile to MO file: `languages/password-reset-guard-LOCALE.mo`

### Testing Your Changes

1. **Test the password reset form**:
   - Visit: `yourlocalsite.com/wp-login.php?action=lostpassword`
   - Verify CAPTCHA appears
   - Test wrong answer (should error)
   - Test correct answer (should work)

2. **Test in different contexts**:
   - Test on different browsers
   - Test on mobile devices
   - Test with different difficulty levels

3. **Check for errors**:
   - Enable WordPress debug in `wp-config.php`:
     ```php
     define( 'WP_DEBUG', true );
     define( 'WP_DEBUG_DISPLAY', false );
     define( 'WP_DEBUG_LOG', true );
     ```
   - Check logs: `wp-content/debug.log`

## Debugging Tips

### Enable WordPress Debug Mode

Add to `wp-config.php`:

```php
// Enable debugging
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );

// Log to file instead of display
define( 'SCRIPT_DEBUG', true );
```

Check logs at: `wp-content/debug.log`

### Use error_log()

In PHP code:

```php
error_log( 'Debug message: ' . print_r( $data, true ) );
```

View in debug.log file.

### Browser Developer Tools

For JavaScript issues:
- Press `F12` to open Developer Tools
- Check **Console** for JS errors
- Check **Network** tab for failed requests

## WordPress Hooks Used

### Actions
- `init` - Plugin initialization
- `admin_menu` - Register admin menu
- `admin_init` - Register settings
- `login_form_lostpassword` - Enqueue scripts on password reset
- `login_enqueue_scripts` - Enqueue styles on login

### Filters
- `lostpassword_form` - Add CAPTCHA to form
- `lostpassword_post` - Validate CAPTCHA
- `wp_lostpassword_errors` - Handle validation errors

## Code Style Guidelines

### PHP
```php
// Use WordPress functions
$value = get_option( 'option_name' );

// Escape output
echo esc_html( $value );

// Proper spacing
if ( $condition ) {
    // code
}

// Use descriptive variable names
$is_captcha_enabled = true;
```

### JavaScript
```javascript
// Wrap in IIFE
(function( document ) {
    'use strict';

    // code here

})( document );
```

### CSS
```css
/* Use prg- prefix */
.prg-captcha-field {
    margin: 15px 0;
    padding: 12px;
}
```

## Testing Checklist

Before submitting a PR or releasing:

- [ ] CAPTCHA appears on password reset form
- [ ] Correct answer is accepted
- [ ] Incorrect answer is rejected
- [ ] Settings page works
- [ ] All difficulty levels work
- [ ] Enable/disable toggle works
- [ ] No JavaScript console errors
- [ ] Mobile responsive
- [ ] Keyboard navigable
- [ ] Screen reader compatible
- [ ] Tested on WordPress 4.9+
- [ ] Tested on latest WordPress
- [ ] Tested on PHP 5.6+ and PHP 8.0+

## Useful Resources

- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [WordPress Security](https://developer.wordpress.org/plugins/security/)
- [PHP Documentation](https://www.php.net/docs.php)

## Getting Help

- Check existing GitHub issues
- Ask in discussions
- Review the contributing guide

---

**Happy developing!**
