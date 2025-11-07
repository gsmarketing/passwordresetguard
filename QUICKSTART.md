# Quick Start Guide - Password Reset Guard

Get Password Reset Guard up and running in 5 minutes.

## Installation (2 minutes)

### Option A: Upload ZIP from GitHub
1. Download latest release from: https://github.com/yourusername/password-reset-guard/releases
2. In WordPress admin: **Plugins** → **Add New** → **Upload Plugin**
3. Select the ZIP file and click **Install Now**
4. Click **Activate Plugin**

### Option B: Manual Upload
1. Download and extract ZIP file
2. Upload `password-reset-guard` folder to `/wp-content/plugins/`
3. In WordPress admin: **Plugins** → Find "Password Reset Guard" → **Activate**

## Configuration (1 minute)

1. Go to **WordPress Settings** → **Password Reset Guard**
2. Ensure **Enable CAPTCHA** is checked ✓
3. Select difficulty level:
   - **Easy**: Numbers 1-10 (accessibility-friendly)
   - **Medium**: Numbers 5-50 (recommended)
   - **Hard**: Numbers 10-99 (maximum protection)
4. Click **Save Changes**

## Test It (2 minutes)

1. Go to: `yoursite.com/wp-login.php?action=lostpassword`
2. You should see a math CAPTCHA field: "Solve the math problem: 3 + 5 = ?"
3. Try entering a wrong answer - you'll get an error ✗
4. Refresh and try the correct answer - it works ✓

## That's It!

Your site is now protected from password reset spam attacks.

## Support

- **Documentation**: See [README.md](README.md)
- **Installation Help**: See [INSTALL.md](INSTALL.md)
- **Report Issues**: Open a GitHub issue

## Key Features

✓ Lightweight (no bloat, no external services)
✓ Simple math CAPTCHA (humans solve it easily)
✓ Three difficulty levels
✓ Mobile-friendly
✓ Accessibility-friendly
✓ Privacy-focused (no tracking)
✓ Easy to disable if needed

---

**Protected from password reset spam with just a math problem!**
