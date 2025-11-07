#!/usr/bin/env node

/**
 * Build script for Password Reset Guard plugin
 *
 * Creates a distributable ZIP file excluding all development files.
 * Usage: npm run build
 *
 * Output: Creates build/password-reset-guard-vX.X.X.zip
 */

const fs = require('fs');
const path = require('path');
const archiver = require('archiver');

// Get version from main plugin file
const pluginFile = fs.readFileSync(
  path.join(__dirname, '../password-reset-guard.php'),
  'utf8'
);

const versionMatch = pluginFile.match(/\*\s*Version:\s*([0-9.]+)/);
if (!versionMatch) {
  console.error('❌ Could not find plugin version in password-reset-guard.php');
  process.exit(1);
}

const version = versionMatch[1];
const zipFileName = `password-reset-guard-v${version}.zip`;
const buildDir = path.join(__dirname, '../build');

console.log(`\n📦 Building Password Reset Guard v${version}`);
console.log(`🎯 Output: ${zipFileName}\n`);

// Create build directory if it doesn't exist
fs.mkdirSync(buildDir, { recursive: true });

// Files and directories to include in the release
const filesToInclude = [
  'password-reset-guard.php',
  'readme.txt',
  'LICENSE',
  'assets',
  'includes',
  'languages',
];

const projectRoot = path.join(__dirname, '..');
const zipPath = path.join(buildDir, zipFileName);

// Remove existing ZIP if it exists
if (fs.existsSync(zipPath)) {
  fs.unlinkSync(zipPath);
}

const output = fs.createWriteStream(zipPath);
const archive = archiver('zip', {
  zlib: { level: 9 },
});

let hasError = false;

output.on('close', () => {
  if (hasError) {
    return;
  }

  const stats = fs.statSync(zipPath);
  const sizeKb = (stats.size / 1024).toFixed(2);

  console.log(`\n✅ Build complete!`);
  console.log(`\n📁 Release package created:`);
  console.log(`   File: ${zipFileName}`);
  console.log(`   Size: ${sizeKb} KB`);
  console.log(`   Path: ./build/${zipFileName}`);

  console.log(`\n📝 Next steps:`);
  console.log(`   1. Commit any changes: git add . && git commit -m "Release v${version}"`);
  console.log(`   2. Create git tag: git tag -a v${version} -m "Release v${version}"`);
  console.log(`   3. Push changes: git push origin main && git push origin v${version}`);
  console.log(`   4. Upload ZIP to GitHub releases at:`);
  console.log(`      https://github.com/gsmarketing/passwordresetguard/releases/new`);
  console.log();
});

archive.on('error', (error) => {
  hasError = true;
  console.error(`\n❌ Archive error: ${error.message}`);
  process.exit(1);
});

output.on('error', (error) => {
  hasError = true;
  console.error(`\n❌ Output stream error: ${error.message}`);
  process.exit(1);
});

// Pipe archive to output
archive.pipe(output);

console.log('📋 Adding files to archive...');

// Add files and directories to the archive
filesToInclude.forEach((file) => {
  const sourcePath = path.join(projectRoot, file);

  if (!fs.existsSync(sourcePath)) {
    console.warn(`  ⚠️  Skipping missing file: ${file}`);
    return;
  }

  if (fs.lstatSync(sourcePath).isDirectory()) {
    // Add directory recursively
    archive.directory(sourcePath, `password-reset-guard/${file}`);
    console.log(`  ✓ ${file}/ added`);
  } else {
    // Add single file
    archive.file(sourcePath, { name: `password-reset-guard/${file}` });
    console.log(`  ✓ ${file} added`);
  }
});

// Finalize the archive
archive.finalize();
