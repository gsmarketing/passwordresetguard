# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Repository Overview

**Password Reset Guard** is a lightweight WordPress plugin (~400 lines of code, zero dependencies) that protects sites from password reset spam attacks using a native math CAPTCHA. The plugin is intentionally simple and minimal, with no external libraries or third-party services.

### Core Philosophy
- **Lightweight**: Zero dependencies, minimal code footprint
- **Secure**: Server-side validation, input sanitization, output escaping
- **Accessible**: Mobile responsive, keyboard navigable, screen reader compatible
- **No Bloat**: Direct WordPress integration without unnecessary abstractions

## Architecture

### Single-Class Design with Smart Hook Loading
The entire plugin functionality is encapsulated in `Password_Reset_Guard` class in [password-reset-guard.php](password-reset-guard.php). **Critical optimization**: The constructor uses conditional hook initialization to avoid unnecessary processing on non-relevant pages.

**Constructor Strategy** (`__construct`):
1. Always load text domain (needed for i18n everywhere)
2. Check `is_admin()` → Load only admin hooks if in WordPress admin
3. Check `is_captcha_enabled()` → Skip all hooks if feature is disabled
4. Check `is_password_reset_page()` → Only load password reset hooks on relevant pages

This approach means:
- Regular page loads: Minimal overhead (1 option check + 1 page detection)
- Admin pages (not settings): Just text domain loading
- Admin settings page: Full hook setup
- Password reset page: CAPTCHA hooks only

**Key class responsibilities**:
1. **Smart Initialization**: Early bailouts based on context
2. **Settings Management**: Admin menu, options registration, form rendering
3. **CAPTCHA Logic**: Generation and server-side validation
4. **Asset Handling**: Lightweight CSS/JS enqueueing

**Key private methods**:
- `is_captcha_enabled()`: Checks WordPress option (early bailout if false)
- `is_password_reset_page()`: Detects lost password form or submission
- `generate_captcha()`: Creates random math problem based on difficulty level
- `calculate_answer()`: Computes correct answer for validation

### Plugin Flow

**On Every Page Load** (all hooks registered):
```
1. Plugin file loaded by WordPress
2. Password_Reset_Guard class instantiated
3. Constructor checks context:
   - Load text domain (always)
   - Load admin hooks? (only if is_admin() == true)
   - Load frontend hooks? (only if is_captcha_enabled() == true)
   - Load password reset hooks? (only if is_password_reset_page() == true)
4. Hooks registered based on context
5. Early returns prevent unnecessary code execution
```

**On Password Reset Page** (specific flow):
```
1. User visits /wp-login.php?action=lostpassword
2. is_password_reset_page() returns true
3. login_enqueue_scripts hook fires → CSS enqueued
4. lostpassword_form filter fires → Math CAPTCHA HTML injected
5. User solves problem (e.g., "7 + 23 = ?")
6. User submits form
7. lostpassword_post filter fires → validate_captcha() called
8. Server-side validation recalculates answer
9. If wrong: Error added to $errors object, form redisplayed
10. If correct: Password reset proceeds normally
```

### Difficulty Levels
Located in `generate_captcha()` method:
- **Easy**: Numbers 1-10 (accessibility-friendly)
- **Medium**: Numbers 5-50 (default, recommended)
- **Hard**: Numbers 10-99 (maximum protection)

For subtraction, num1 is swapped with num2 if needed to prevent negative results.

### Settings Storage
Both settings use WordPress options API (no custom tables):
- `prg_enable_captcha` (boolean, default: 1)
- `prg_difficulty` (string: 'easy'|'medium'|'hard', default: 'medium')

Retrieved via `get_option()` with defaults in Settings → Password Reset Guard.

## Development Commands

### Local WordPress Setup
```bash
# Development assumes a local WordPress installation
# Install via Local.app, XAMPP, Docker, or similar
# Plugin should be in: /wp-content/plugins/password-reset-guard/
```

### Code Quality Checks
```bash
# PHP CodeSniffer (WordPress standards)
# Requires: composer require --dev squizlabs/php_codesniffer wpcsstandards/wpcs
phpcs password-reset-guard.php --standard=phpcs.xml.dist

# Fix PHPCS issues automatically
phpcbf password-reset-guard.php --standard=phpcs.xml.dist
```

### Testing
No automated tests currently. Manual testing checklist in [DEV_SETUP.md](DEV_SETUP.md#testing-checklist):
- Test CAPTCHA appears on password reset form
- Test all three difficulty levels
- Test enable/disable toggle
- Test wrong answers are rejected
- Test correct answers proceed
- Test mobile responsiveness
- Test keyboard navigation
- Test no console errors

### Building Release ZIP
```bash
# Create deployment-ready ZIP (used for WordPress.org and GitHub releases)
# Excludes all dev files while keeping user-facing documentation
zip -r password-reset-guard.zip . \
  -x "*.git/*" \
  "node_modules/*" \
  ".docs/*" \
  ".gitignore" \
  ".editorconfig" \
  ".github/*" \
  "*.code-workspace" \
  "CLAUDE.md"
```

Note: `.docs/` folder is excluded because it contains development documentation, not needed for end users.

## File Structure

- **[password-reset-guard.php](password-reset-guard.php)**: Main plugin file (only PHP file with logic)
- **[assets/css/captcha.css](assets/css/captcha.css)**: CAPTCHA field styling (mobile responsive, accessibility)
- **[assets/js/captcha.js](assets/js/captcha.js)**: Client-side form validation (vanilla JS, no dependencies)
- **[includes/helpers.php](includes/helpers.php)**: Utility functions for future expansion (currently minimal)
- **[languages/password-reset-guard.pot](languages/password-reset-guard.pot)**: Internationalization template
- **Documentation**: 11 guides in root directory (README.md, INSTALL.md, DEV_SETUP.md, etc.)

## Key Constants

Defined at plugin load:
- `PRG_VERSION`: Current plugin version (e.g., '1.0.0')
- `PRG_PLUGIN_DIR`: Absolute filesystem path to plugin directory
- `PRG_PLUGIN_URL`: HTTP URL to plugin directory (for asset loading)

## WordPress Hooks Used

All hooks are registered conditionally in `__construct()`. Only relevant hooks fire based on page context.

### Always Registered
- `init`: Load text domain for translations (always needed for i18n)

### Admin Context Only (when `is_admin() === true`)
- `admin_menu`: Register "Password Reset Guard" settings page
- `admin_init`: Register CAPTCHA enable/disable and difficulty options

### Password Reset Page Only (when `is_password_reset_page() === true` AND `is_captcha_enabled() === true`)
- `login_enqueue_scripts`: Enqueue CAPTCHA CSS
- `lostpassword_form` (filter): Inject CAPTCHA HTML into password reset form
- `lostpassword_post` (filter): Validate CAPTCHA answer before processing reset

**Early Bailout Logic**:
- If not in admin and CAPTCHA disabled → Return immediately, no hooks registered
- If not password reset page → Only text domain hook registered
- If on password reset page → Only password reset hooks registered

## Security Implementation

**Input Validation**:
- CAPTCHA numbers: Cast to `(int)` before calculation
- Operation: Sanitized with `sanitize_text_field()`
- User answer: Cast to `(int)` for comparison

**Output Escaping**:
- HTML content: `esc_html()` and `esc_html_e()`
- Attributes: `esc_attr()` and `esc_attr_e()`
- Plugin text domain: 'password-reset-guard' (all strings i18n-ready)

**Validation Location**:
- Server-side: `validate_captcha()` filter (authoritative)
- Client-side: `assets/js/captcha.js` (UX only, not security-critical)

## Internationalization (i18n)

**Text Domain**: 'password-reset-guard'
**Language Files Path**: `/languages/`
**Template File**: [language/password-reset-guard.pot](language/password-reset-guard.pot)

All user-facing strings use `__()`, `_e()`, or `esc_html__()` with text domain. Compatible with WordPress.org translation platform.

## Common Development Tasks

### Adding a New Setting
1. Register in `register_settings()`: `register_setting( 'password_reset_guard', 'prg_new_option' )`
2. Add settings field with render callback
3. Retrieve with: `get_option( 'prg_new_option', 'default_value' )`

### Adjusting CAPTCHA Difficulty Ranges
Modify `generate_captcha()` switch statement:
```php
case 'easy':
    $num1 = wp_rand( 1, 10 );  // Change these ranges
    $num2 = wp_rand( 1, 10 );
    break;
```

### Adding New Math Operations
1. Add to `$operations` array in `generate_captcha()`
2. Add corresponding case in `calculate_answer()`
3. Test edge cases (negative results, division by zero, etc.)

### Customizing CAPTCHA HTML
The HTML is generated in `add_captcha_field()` using `sprintf()`. The span shows the visible question, hidden inputs pass values to server. Changes here affect both display and validation logic.

## CSS Classes

All CSS uses `prg-` prefix (Password Reset Guard):
- `.prg-captcha-field`: Main container
- `.prg-captcha-question`: The visible math problem display
- `.prg-error`: Applied to input when validation fails (client-side only)

CSS is enqueued only on login pages when CAPTCHA is enabled.

## JavaScript Behavior

**Client-side validation** (`assets/js/captcha.js`):
- Runs on form submit to catch empty/non-numeric answers
- Prevents form submission if validation fails
- Adds `.prg-error` class for styling feedback
- Removes error class when user starts typing again
- Wraps in IIFE to avoid global scope pollution

Note: Client-side validation is UX only. Server-side validation in `validate_captcha()` is authoritative (cannot be bypassed).

## Plugin Initialization

```php
// At end of password-reset-guard.php
new Password_Reset_Guard();
```

The class is instantiated immediately on load. Constructor hooks into WordPress. No activation/deactivation hooks (settings persist in wp_options).

## Performance Notes

- **No database queries**: Uses WordPress options API (cached)
- **No external API calls**: Math computed locally
- **CSS size**: ~1.2 KB
- **JS size**: ~2 KB (vanilla, no dependencies)
- **PHP code**: ~400 lines

Impact on password reset page load: negligible (<1ms additional).

## Debugging

Enable WordPress debug mode in `wp-config.php`:
```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );
```

Check logs at: `wp-content/debug.log`

Use `prg_log()` helper function for plugin-specific logging (only logs when WP_DEBUG is true).

## Extending the Plugin

The plugin is intentionally minimal. To extend:
- Add new helper functions to [includes/helpers.php](includes/helpers.php)
- Use existing hooks: `lostpassword_form`, `lostpassword_post`, etc.
- New features should maintain zero-dependency philosophy
- Keep CAPTCHA logic simple (math-based is the core feature)

## Browser/Version Compatibility

- **WordPress**: 4.9+
- **PHP**: 5.6+ (WordPress minimum)
- **JavaScript**: Vanilla JS, works in all modern browsers and IE 11
- **CSS**: CSS3, no preprocessor (vanilla CSS only)

## Related Documentation

For detailed guidance, see:
- [README.md](README.md): User documentation and features
- [INSTALL.md](INSTALL.md): Installation and troubleshooting
- [DEV_SETUP.md](DEV_SETUP.md): Development environment and workflow
- [CONTRIBUTING.md](CONTRIBUTING.md): Contributing guidelines
- [DEPLOYMENT.md](DEPLOYMENT.md): Release and WordPress.org submission
- [PROJECT_SUMMARY.md](PROJECT_SUMMARY.md): Complete project overview

## Quick Reference: Password Reset CAPTCHA Flow

1. **Plugin Init**: `new Password_Reset_Guard()` in password-reset-guard.php
2. **Load Hooks**: Constructor registers all WordPress actions/filters
3. **User Visits Reset**: Hits `/wp-login.php?action=lostpassword`
4. **Filter Applied**: `lostpassword_form` filter injects CAPTCHA HTML via `add_captcha_field()`
5. **Assets Loaded**: CSS/JS enqueued via `enqueue_captcha_*()` methods
6. **User Solves**: Math problem shown via `.prg-captcha-question` span
7. **Form Submits**: JavaScript validates non-empty numeric answer (UX feedback)
8. **Server Validates**: `validate_captcha()` filter recalculates correct answer
   - If wrong: Error added to `$errors` object, form redisplayed
   - If correct: Password reset proceeds normally
9. **Result**: Either error message or password reset email sent

This flow ensures CAPTCHA cannot be bypassed (server-side is authoritative).

---

## Repository Organization

### Public Files (in GitHub)
- `README.md`, `QUICKSTART.md`, `INSTALL.md`, `CHANGELOG.md` - User-facing docs
- `password-reset-guard.php`, `assets/`, `languages/`, `includes/` - Plugin code
- `LICENSE`, `package.json`, `.github/` - Standard repo files
- `CLAUDE.md` - This file (for future Claude Code instances)

### Development Files (.docs/ folder, gitignored)
- `.docs/DEV_SETUP.md` - Development environment setup
- `.docs/CONTRIBUTING.md` - Contribution guidelines
- `.docs/DEPLOYMENT.md` - Release process
- `.docs/PROJECT_SUMMARY.md` - Technical deep dive
- `.docs/DOCUMENTATION_INDEX.md` - Documentation guide
- `.docs/COMPLETION_SUMMARY.md` - Build notes from v1.0.0
- `.docs/STRUCTURE.txt` - File organization

**Note**: `.docs/` is in `.gitignore` to keep the public repo clean. Developers should reference these files locally, but they don't appear in GitHub commits or releases.
