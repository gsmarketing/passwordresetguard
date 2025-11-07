# Installation Guide - Password Reset Guard

This guide will walk you through installing and setting up Password Reset Guard on your WordPress site.

## Requirements

Before you begin, make sure your server meets these minimum requirements:

- **WordPress**: 4.9 or higher
- **PHP**: 5.6 or higher
- **File Permissions**: Write access to `/wp-content/plugins/` directory

## Installation Methods

### Method 1: WordPress Plugin Upload (Easiest)

1. **Download the plugin**
   - Visit the [Releases page](https://github.com/yourusername/password-reset-guard/releases)
   - Download the latest `password-reset-guard.zip` file

2. **Upload via WordPress Admin**
   - Log in to your WordPress dashboard
   - Go to **Plugins** → **Add New**
   - Click the **Upload Plugin** button
   - Select the downloaded ZIP file
   - Click **Install Now**

3. **Activate the plugin**
   - Click the **Activate Plugin** button
   - Or go to **Plugins** and find "Password Reset Guard", then click **Activate**

4. **Configure settings**
   - Go to **Settings** → **Password Reset Guard**
   - Adjust settings as needed
   - Click **Save Changes**

### Method 2: Manual FTP/File Upload

1. **Download and extract**
   - Download the ZIP file from releases
   - Extract it on your computer
   - You'll have a folder named `password-reset-guard`

2. **Upload via FTP**
   - Use an FTP client (e.g., FileZilla) to connect to your server
   - Navigate to `/wp-content/plugins/`
   - Upload the entire `password-reset-guard` folder

3. **Activate in WordPress**
   - Log in to your WordPress dashboard
   - Go to **Plugins**
   - Find "Password Reset Guard" in the list
   - Click **Activate**

4. **Configure settings**
   - Go to **Settings** → **Password Reset Guard**
   - Adjust settings as needed
   - Click **Save Changes**

### Method 3: Git Clone (For Developers)

1. **Clone the repository**
   ```bash
   cd /wp-content/plugins/
   git clone https://github.com/yourusername/password-reset-guard.git
   ```

2. **Activate in WordPress**
   - Log in to your WordPress dashboard
   - Go to **Plugins**
   - Find "Password Reset Guard"
   - Click **Activate**

3. **Configure settings**
   - Go to **Settings** → **Password Reset Guard**
   - Adjust settings as needed
   - Click **Save Changes**

## Post-Installation Setup

### Enable CAPTCHA Protection

1. Go to **WordPress Dashboard** → **Settings** → **Password Reset Guard**

2. Make sure **Enable CAPTCHA** is checked (enabled)

3. Choose your preferred **Difficulty Level**:
   - **Easy**: Numbers 1-10 (recommended for accessibility)
   - **Medium**: Numbers 5-50 (recommended, balanced security)
   - **Hard**: Numbers 10-99 (maximum protection)

4. Click **Save Changes**

### Test the Installation

1. Visit your site's password reset page:
   - **URL**: `yoursite.com/wp-login.php?action=lostpassword`
   - Or use the "Lost Password?" link on the login page

2. You should see a math CAPTCHA field added to the form

3. Try submitting a wrong answer - you should get an error message

4. Try submitting the correct answer - the form should process normally

## Troubleshooting

### Plugin doesn't appear in plugins list

**Solution:**
- Make sure the plugin folder is named `password-reset-guard`
- Check that `password-reset-guard.php` is in the plugin folder root
- Verify file permissions (should be readable by web server)
- Check WordPress error logs in `/wp-content/debug.log`

### CAPTCHA not appearing on password reset page

**Solution:**
- Go to **Settings** → **Password Reset Guard**
- Make sure "Enable CAPTCHA" is checked
- Clear your browser cache
- Try a different browser to rule out cache issues
- Check WordPress error logs for PHP errors

### "CAPTCHA answer is incorrect" error for correct answers

**Solution:**
- This is usually a form validation issue
- Make sure you're only entering the number (e.g., `23` not `23 `)
- Refresh the page - the math problem regenerates
- Check that your server's PHP has proper arithmetic operations

### Error: "You do not have permission to access this page"

**Solution:**
- You must be logged in as an administrator to access settings
- Go to **Settings** → **Password Reset Guard** (not as a visitor)
- Only site administrators can change plugin settings

## Uninstallation

If you need to remove Password Reset Guard:

1. Go to **Plugins** in WordPress admin
2. Find "Password Reset Guard"
3. Click **Deactivate**
4. Click **Delete**
5. Confirm the deletion

The plugin will be completely removed, and the password reset form will function normally without the CAPTCHA.

## Upgrading

To upgrade to a new version:

1. **From GitHub Releases:**
   - Download the new version ZIP
   - Go to **Plugins** → find Password Reset Guard
   - Click **Delete** to remove the old version
   - Upload the new version using Method 1 above

2. **Using Git:**
   ```bash
   cd /wp-content/plugins/password-reset-guard
   git pull origin main
   ```

No database changes are made by this plugin, so upgrades are safe and don't require data migration.

## Getting Help

If you encounter issues:

1. **Check this guide** - your issue might be listed above
2. **Check the README.md** - for general usage information
3. **Open an Issue on GitHub** - include:
   - Your WordPress version
   - Your PHP version
   - Steps to reproduce the issue
   - Any error messages from the debug log

## Security Notes

Password Reset Guard:
- Does not collect or store user data
- Does not make external API calls
- Does not use tracking or analytics
- Does not require any special permissions
- Follows WordPress security best practices

Your password reset emails are handled by WordPress as usual - this plugin only adds a human verification step.

---

**Installation complete! Your site is now protected from password reset spam.**
