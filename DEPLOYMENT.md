# Deployment Guide

Guide for deploying Password Reset Guard to GitHub and WordPress.org.

## GitHub Setup

### 1. Create a GitHub Repository

1. Go to https://github.com/new
2. Repository name: `password-reset-guard`
3. Description: "Lightweight CAPTCHA protection against password reset spam"
4. Choose Public (for open-source plugin)
5. Add README.md, .gitignore (we already have these)
6. Click **Create repository**

### 2. Initialize Git Locally

```bash
cd /home/gary/projects/wpreset
git init
git add .
git commit -m "Initial commit: Password Reset Guard v1.0.0

- Lightweight math CAPTCHA implementation
- Three difficulty levels
- Admin settings page
- Full internationalization support
- Mobile responsive design
- Security best practices"
```

### 3. Connect to GitHub

```bash
git remote add origin https://github.com/yourusername/password-reset-guard.git
git branch -M main
git push -u origin main
```

## Creating Releases

### 1. Tag a Release

```bash
git tag -a v1.0.0 -m "Password Reset Guard v1.0.0 - Initial Release"
git push origin v1.0.0
```

### 2. Create Release on GitHub

1. Go to your repository on GitHub
2. Click **Releases** → **Create a new release**
3. Tag version: `v1.0.0`
4. Release title: `Password Reset Guard v1.0.0`
5. Release notes:

```markdown
## Password Reset Guard v1.0.0

Initial stable release featuring:

### Features
- Native math CAPTCHA (addition, subtraction, multiplication)
- Three difficulty levels (Easy, Medium, Hard)
- Lightweight and dependency-free
- Admin settings page
- Full internationalization
- Mobile responsive
- Accessibility friendly

### Security
- Server-side validation
- Input sanitization
- No external API calls
- No data collection

### Requirements
- WordPress 4.9+
- PHP 5.6+

[Installation Guide](https://github.com/yourusername/password-reset-guard#installation)
[Documentation](https://github.com/yourusername/password-reset-guard)

**Download**: [password-reset-guard.zip](link-to-zip)
```

6. Attach the plugin ZIP file
7. Click **Publish release**

## Creating Release ZIP

```bash
# Create a clean zip without git files
zip -r password-reset-guard.zip . \
  -x "*.git/*" \
  "node_modules/*" \
  ".gitignore" \
  ".editorconfig" \
  ".github/*" \
  "*.code-workspace" \
  "DEV_SETUP.md" \
  "DEPLOYMENT.md"
```

## Publishing to WordPress.org Plugin Directory

### Requirements
- WordPress.org account (create at wordpress.org)
- SVN access to plugins directory
- Plugin must meet WordPress standards

### Step 1: Prepare Plugin

1. Ensure your `password-reset-guard.php` header is correct:

```php
<?php
/**
 * Plugin Name: Password Reset Guard
 * Description: Lightweight CAPTCHA protection against password reset spam
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL-2.0-or-later
 * Text Domain: password-reset-guard
 * Domain Path: /languages
 */
```

2. Run PHPCS to ensure WordPress standards:

```bash
phpcs password-reset-guard.php --standard=phpcs.xml.dist
```

### Step 2: Submit Plugin

1. Go to https://wordpress.org/plugins/submit/
2. Upload your plugin ZIP
3. Fill out the form:
   - Plugin name
   - Short description
   - Long description
   - Support forum info
   - Author info
4. Accept terms
5. Submit for review

### Step 3: WordPress Plugin Directory

Once approved, manage your plugin at:
https://wordpress.org/plugins/password-reset-guard/

You can:
- Upload new versions via web or SVN
- Manage documentation
- View statistics
- Respond to reviews

## Updating Versions

### Local Update

```bash
# Update version in password-reset-guard.php
# Update CHANGELOG.md with new version notes
# Update package.json version

git add .
git commit -m "Bump version to 1.0.1"
git tag -a v1.0.1 -m "Version 1.0.1"
git push origin main
git push origin v1.0.1
```

### GitHub Release

Create release on GitHub with release notes.

### WordPress.org Update

**Via Web:**
1. Go to plugin edit page
2. Upload new ZIP file

**Via SVN:**
```bash
svn co https://plugins.svn.wordpress.org/password-reset-guard/ password-reset-guard-svn
cd password-reset-guard-svn
# Copy new files
svn add [new files]
svn commit -m "Version 1.0.1"
```

## Maintenance Checklist

### For Each Release:

- [ ] Update version in `password-reset-guard.php`
- [ ] Update `CHANGELOG.md` with notes
- [ ] Update `package.json` version
- [ ] Test on WordPress 4.9+
- [ ] Test on latest WordPress
- [ ] Test on PHP 5.6+ and 8.0+
- [ ] Run PHPCS
- [ ] Test on mobile devices
- [ ] Create git tag
- [ ] Create GitHub release
- [ ] Update WordPress.org (if published)

## Backups and Disaster Recovery

### Local Backup

```bash
# Create backup before major changes
cp -r password-reset-guard/ password-reset-guard-backup-$(date +%Y%m%d)
```

### Git History

Git stores all history. To revert to a previous version:

```bash
git log --oneline
git revert [commit-hash]
```

## Security Best Practices

- [ ] Always escape output with `esc_html()`, `esc_attr()`, etc.
- [ ] Validate all inputs
- [ ] Use nonces for form submissions (if added)
- [ ] Never store sensitive data
- [ ] Follow WordPress security practices
- [ ] Test against OWASP Top 10
- [ ] Keep dependencies up to date

## Continuous Integration (Optional)

Consider adding GitHub Actions for:
- PHPCS code quality checks
- WordPress compatibility testing
- Automated release creation

Example `.github/workflows/phpcs.yml`:

```yaml
name: PHPCS Checks
on: [push, pull_request]
jobs:
  phpcs:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - uses: php-actions/phpcs@v1
        with:
          standard: phpcs.xml.dist
```

## Support Resources

- [WordPress Plugin Handbook](https://developer.wordpress.org/plugins/)
- [WordPress SVN Documentation](https://developer.wordpress.org/plugins/wordpress-org/how-your-plugin-is-hosted/)
- [Plugin Security](https://developer.wordpress.org/plugins/security/)

## Questions?

- Check WordPress.org plugin documentation
- Review other popular plugin repositories
- Ask in WordPress plugin forums

---

**Your plugin is ready to share with the world!**
