# Contributing to Password Reset Guard

Thank you for your interest in contributing to Password Reset Guard! This document provides guidelines and instructions for contributing.

## Code of Conduct

- Be respectful and inclusive
- Provide constructive feedback
- Focus on the code, not the person
- Help others learn and grow

## Getting Started

### Prerequisites
- WordPress 4.9+
- PHP 5.6+
- Git
- Basic knowledge of PHP and WordPress plugin development

### Setup Development Environment

1. Clone the repository:
   ```bash
   git clone https://github.com/yourusername/password-reset-guard.git
   cd password-reset-guard
   ```

2. Set up a local WordPress installation
3. Symlink or copy the plugin to your `/wp-content/plugins/` directory
4. Activate the plugin in WordPress admin

## Making Changes

### File Structure
```
password-reset-guard/
├── password-reset-guard.php     # Main plugin file
├── assets/
│   ├── css/captcha.css          # Styling
│   └── js/captcha.js            # Client-side logic
├── languages/                   # Translation files
├── includes/                    # Helper functions (future)
├── README.md
├── CHANGELOG.md
├── LICENSE
└── .gitignore
```

### Coding Standards

- **PHP**: Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- **JavaScript**: Follow [WordPress JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/)
- **CSS**: Use clear, descriptive class names with `prg-` prefix

### Style Guide

#### PHP
```php
// Always use WordPress functions over native PHP
$value = get_option( 'option_name' );

// Escape output
echo esc_html( $value );

// Proper spacing and formatting
if ( condition ) {
    // code
}
```

#### JavaScript
```javascript
// Use strict mode
'use strict';

// Wrap in IIFE to avoid globals
(function( document ) {
    // code
})( document );

// Use descriptive variable names
const answerField = document.getElementById( 'prg_captcha' );
```

#### CSS
```css
/* Use prg- prefix for all classes */
.prg-captcha-field {
    /* properties */
}

/* Group related properties */
.prg-captcha-field input {
    padding: 8px 10px;
    border: 1px solid #999;
    border-radius: 4px;
}
```

### Security Considerations

When submitting code, ensure:
- Input validation on all user inputs
- Output escaping with appropriate WordPress functions
- No direct database queries (use WordPress APIs)
- No hardcoded admin IDs or sensitive data
- Proper nonce verification if handling forms
- Follow WordPress Security Best Practices

### Testing

Before submitting a pull request:
1. Test on multiple WordPress versions (4.9+, latest)
2. Test on multiple PHP versions (5.6+, latest)
3. Test with different difficulty levels
4. Test on mobile devices
5. Test keyboard navigation
6. Test with screen readers

## Submitting Changes

### Creating a Branch
```bash
git checkout -b feature/your-feature-name
# or
git checkout -b bugfix/issue-number
```

### Commit Messages
- Use clear, descriptive commit messages
- Start with a verb (Add, Fix, Update, etc.)
- Keep commits focused on a single change

Examples:
- ✅ `Add support for custom CAPTCHA messages`
- ✅ `Fix validation error on form resubmission`
- ❌ `Update stuff`
- ❌ `WIP - stuff`

### Submitting a Pull Request

1. Push your branch to your fork:
   ```bash
   git push origin feature/your-feature-name
   ```

2. Open a pull request against the `main` branch

3. In the PR description, include:
   - What problem does this solve?
   - What changes were made?
   - How was this tested?
   - Any breaking changes?

4. Link related issues if applicable

### PR Template
```markdown
## Description
Brief description of changes.

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
How was this tested?
- [ ] Tested on WordPress 4.9
- [ ] Tested on WordPress latest
- [ ] Tested mobile responsiveness
- [ ] Tested keyboard navigation

## Checklist
- [ ] Code follows WordPress standards
- [ ] All inputs are validated
- [ ] All output is escaped
- [ ] No console errors
- [ ] Documentation updated if needed
```

## Types of Contributions

### Bug Reports
- Use the issue tracker
- Provide clear reproduction steps
- Include WordPress version and PHP version
- Include any error messages

### Feature Requests
- Open an issue with `[FEATURE]` prefix
- Explain the use case
- Keep it simple (remember our philosophy!)

### Documentation
- Fix typos and clarifications
- Add examples
- Improve readability

### Code
- Bug fixes
- Performance improvements
- Accessibility enhancements
- i18n improvements

## Translation Support

To add translations:

1. Update the POT file with new strings
2. Create language-specific PO/MO files
3. Place in `/languages/` directory
4. Name format: `password-reset-guard-LOCALE.po`

Example: `password-reset-guard-es_ES.po` for Spanish (Spain)

## Reporting Security Issues

**Do not open public issues for security vulnerabilities.**

Please email security details privately to the maintainer instead.

## Questions?

- Check existing issues and documentation
- Open a discussion/issue with your question
- Be patient for responses (maintained by volunteers)

## License

By contributing, you agree that your contributions will be licensed under the GPL v2.0 or later.

---

Thank you for making Password Reset Guard better! 🎉
