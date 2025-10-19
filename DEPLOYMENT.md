# Deployment Setup Guide

This document describes how to set up automated deployment for the GKK Anmälan application.

## Overview

Build assets (JavaScript, CSS) are now automatically built and deployed via GitHub Actions when changes are pushed to the `master` or `main` branch. This keeps the source control clean and removes the need to include build artifacts in the repository.

## Setting Up GitHub Secrets

To enable automatic deployment, you need to configure FTP credentials as GitHub secrets:

1. Go to the repository on GitHub
2. Navigate to **Settings** → **Secrets and variables** → **Actions**
3. Click **New repository secret**
4. Add the following three secrets:

### Required Secrets

| Secret Name | Description | Example |
|------------|-------------|---------|
| `FTP_SERVER` | FTP server hostname | `ftp.one.com` |
| `FTP_USERNAME` | FTP username for authentication | `your-username` |
| `FTP_PASSWORD` | FTP password for authentication | `your-password` |

## How It Works

When you push code to `master` or `main`:

1. GitHub Actions checks out the code
2. Sets up Node.js 20
3. Installs npm dependencies with `npm ci`
4. Builds production assets with `npm run build`
5. Deploys the `public/` directory to the server via FTP

The deployment automatically excludes files that shouldn't be overwritten:
- `.htaccess`
- `index.php`
- `favicon.*`
- `appIconGKK.png`
- `qr.png`
- `robots.txt`
- And other system files

## Manual Deployment

The workflow can also be triggered manually:

1. Go to the **Actions** tab on GitHub
2. Select the "Build and Deploy" workflow
3. Click **Run workflow**
4. Choose the branch and click **Run workflow**

## Local Development

Build assets are now ignored by git (added to `.gitignore`). To build locally:

```bash
npm install
npm run build
```

The built files will appear in:
- `public/build/` - Vite build output with versioned assets and manifest

This directory is automatically excluded from version control.

## Backend Deployment

Backend code (PHP, Laravel) still needs to be deployed manually:

1. SSH into the server
2. Navigate to the application directory
3. Run `bin/update.sh` to pull latest code and refresh Laravel caches
4. If Composer dependencies changed, run `php composer.phar install --no-dev`

The `update.sh` script no longer copies build assets since they're deployed automatically by GitHub Actions.

## Troubleshooting

### Deployment Fails

Check the GitHub Actions logs:
1. Go to the **Actions** tab
2. Click on the failed workflow run
3. Review the logs for error messages

Common issues:
- Incorrect FTP credentials (verify secrets)
- FTP server not accessible
- Incorrect server directory path

### Assets Not Updating on Server

1. Check that the workflow completed successfully in the Actions tab
2. Verify FTP credentials are correct
3. Check server file permissions
4. Manually trigger the workflow to force a deployment

### Build Fails

If `npm run build` fails:
1. Check the Actions logs for the specific error
2. Try building locally to reproduce the issue
3. Ensure `package.json` and `package-lock.json` are up to date
