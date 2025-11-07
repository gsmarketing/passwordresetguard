# Password Reset Guard

A lightweight WordPress plugin that protects your site from password reset spam and bot attacks using a simple native math CAPTCHA.

## Features

- **Lightweight**: Minimal code, zero dependencies, no bloat
- **Native Math CAPTCHA**: Simple addition, subtraction, and multiplication problems
- **Easy to Use**: Just solve a quick math problem to reset your password
- **Configurable**: Three difficulty levels (Easy, Medium, Hard)
- **No Database**: Uses WordPress options, no extra tables needed
- **Accessibility**: Works with screen readers and keyboard navigation
- **Mobile Friendly**: Responsive design that works on all devices
- **Privacy Focused**: No third-party services or tracking

## Installation

### From GitHub Releases

1. Download the latest release ZIP from the [Releases page](https://github.com/yourusername/password-reset-guard/releases)
2. Go to **WordPress Admin Dashboard** → **Plugins** → **Add New**
3. Click **Upload Plugin** and select the downloaded ZIP file
4. Click **Install Now** and then **Activate Plugin**

### Manual Installation

1. Clone or download this repository
2. Rename the folder to `password-reset-guard`
3. Upload the entire folder to `/wp-content/plugins/`
4. Go to **WordPress Admin Dashboard** → **Plugins**
5. Find "Password Reset Guard" and click **Activate**

## Configuration

After activation, go to **Settings** → **Password Reset Guard** to configure:

- **Enable CAPTCHA**: Toggle CAPTCHA protection on/off
- **Difficulty Level**:
  - **Easy**: Numbers 1-10 (good for accessibility)
  - **Medium**: Numbers 5-50 (recommended, balanced)
  - **Hard**: Numbers 10-99 (maximum protection)

## How It Works

When a user visits the password reset page (`wp-login.php?action=lostpassword`), they will see a simple math problem added to the form. They must solve it correctly before the form can be submitted.

### Example
```
Solve the math problem: 7 + 23 = ?
[Answer field]
```

The plugin:
1. Generates a random math problem on page load
2. Validates the answer on form submission
3. Shows an error message if the answer is incorrect
4. Allows password reset only if the CAPTCHA is solved

## Why This Approach?

Password reset spam is a widespread WordPress security issue. Attackers use bots to:
- Flood your site with hundreds of password reset requests
- Overload your email server
- Create a poor user experience

Password Reset Guard stops these attacks with a simple human verification that:
- **Stops automated bots** (they can't solve math problems)
- **Doesn't require third-party services** (no reCAPTCHA calls)
- **Works instantly** (no waiting for verification emails)
- **Minimal overhead** (pure PHP/JavaScript, no API calls)

## Requirements

- WordPress 4.9+
- PHP 5.6+
- No additional plugins or services required

## Security Notes

- CAPTCHA tokens are validated server-side
- All form inputs are sanitized and validated
- No sensitive data is stored
- Follows WordPress security best practices

## Troubleshooting

### CAPTCHA not appearing
- Make sure the plugin is activated
- Check that "Enable CAPTCHA" is toggled on in settings
- Clear your browser cache

### Form keeps saying answer is wrong
- Make sure you're entering only the numeric answer
- Check that you're using the numbers shown in the math problem
- Try refreshing the page and solving the new problem

## Support

If you encounter any issues:
1. Check this README for common solutions
2. Open an [Issue on GitHub](https://github.com/yourusername/password-reset-guard/issues)
3. Include:
   - Your WordPress version
   - Your PHP version
   - What you were trying to do when the issue occurred

## Contributing

We welcome contributions! Please:
1. Fork this repository
2. Create a feature branch (`git checkout -b feature/improvement`)
3. Make your changes
4. Submit a pull request

## License

This plugin is licensed under the GPL v2.0 or later. See [LICENSE](LICENSE) for details.

## Changelog

### Version 1.0.0
- Initial release
- Math CAPTCHA with three difficulty levels
- Admin settings page
- Full i18n support for translations
- Responsive design with accessibility features

## Support This Project

If you find Password Reset Guard helpful, consider:
- Leaving a review
- Starring the repository
- Reporting issues you encounter
- Contributing improvements

---

**Created for WordPress sites suffering from password reset spam. Simple, effective, no bloat.**
