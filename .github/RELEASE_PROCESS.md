# Automated Release Process

This repository uses GitHub Actions to automatically build and release the plugin when you create a version tag.

## How It Works

1. You create a git tag (e.g., `v0.1.0`)
2. You push the tag to GitHub
3. GitHub Actions automatically:
   - Detects the tag
   - Creates a clean ZIP archive (excluding dev files)
   - Creates a GitHub Release with the ZIP attached
   - Generates a release description

## Step-by-Step Release Process

### Option A: Using Command Line (Recommended)

```bash
# 1. Make sure all changes are committed
git status

# 2. Create a version tag
git tag v0.1.0

# 3. Push the tag to GitHub
git push origin v0.1.0

# That's it! GitHub Actions will handle the rest.
```

### Option B: Using GitHub Web Interface

1. Go to your repository on GitHub.com
2. Click **Releases** (on the right sidebar)
3. Click **Create a new release**
4. Enter tag: `v0.1.0`
5. Click **Publish release**
6. GitHub Actions will automatically attach the ZIP file

## Semantic Versioning

Follow semantic versioning for tags:

- **v0.1.0** - Initial release, pre-release stage
- **v1.0.0** - First stable release
- **v1.1.0** - Minor update (new features, backward compatible)
- **v1.1.1** - Patch release (bug fixes only)
- **v2.0.0** - Major release (breaking changes)

## What Gets Excluded from the ZIP

The workflow excludes development files to keep distributions clean:

```
.github/          (GitHub-specific files)
.git/             (Git history)
.gitignore        (Git config)
.docs/            (Development docs)
.editorconfig     (Editor config)
.wporg-config.json (WP.org submission config)
phpcs.xml.dist    (Code standards config)
*.code-workspace  (VSCode workspace)
CLAUDE.md         (Claude Code instructions)
package.json      (Node.js config)
node_modules/     (NPM packages if present)
```

## What Gets Included in the ZIP

The distribution ZIP contains only what users need:

```
password-reset-guard/
├── password-reset-guard.php    (Main plugin file)
├── readme.txt                   (Plugin documentation)
├── LICENSE                      (GPL-2.0-or-later)
├── assets/
│   ├── css/
│   │   └── captcha.css
│   ├── js/
│   │   └── captcha.js
│   └── README.md               (Asset guidelines)
├── includes/
│   └── helpers.php
└── languages/
    └── password-reset-guard.pot
```

## Workflow File

The workflow is defined in `.github/workflows/release.yml` and triggers on any tag matching `v*`.

### To modify what's included/excluded:

Edit `.github/workflows/release.yml` and update the `Copy plugin files` step:

```yaml
- name: Copy plugin files (excluding unwanted folders/files)
  run: |
    # Add more exclusions like this:
    rm -rf password-reset-guard/some-folder
```

## Testing the Workflow (Optional)

To test without creating a real release:

1. Create a test tag: `git tag test-v0.1.0`
2. Push it: `git push origin test-v0.1.0`
3. Watch it in the **Actions** tab on GitHub
4. Delete when done: `git tag -d test-v0.1.0 && git push origin :test-v0.1.0`

## Troubleshooting

**Release not creating automatically?**
- Check the **Actions** tab on GitHub for workflow errors
- Verify tag format starts with `v` (e.g., `v0.1.0`)
- Ensure `GITHUB_TOKEN` has permissions (usually automatic)

**ZIP file is too large?**
- Check what's being included in the workflow
- Add more exclusions for build artifacts or vendor folders

**Need to edit release after creation?**
- Releases can be edited on GitHub.com
- Workflow won't run again unless you push a new tag

## WordPress.org Submission

Once you have a release ZIP:

1. Download from the GitHub Release page
2. Submit to WordPress.org via SVN (or use their upload tool)
3. Reference: https://developer.wordpress.org/plugins/wordpress-org/

---

That's it! Your releases are now fully automated. 🚀
