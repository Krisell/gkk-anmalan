# Deployment Setup Guide

This document describes how to set up automated deployment for the GKK Anmälan application.

## Overview

Build assets (JavaScript, CSS) are now automatically built and published to GitHub Releases via GitHub Actions when changes are pushed to the `master` or `main` branch. The server deployment script automatically downloads the correct build assets for the current commit, keeping source control clean and enabling easy rollbacks.

## How It Works

When you push code to `master` or `main`:

1. GitHub Actions checks out the code
2. Sets up Node.js 20
3. Installs npm dependencies with `npm ci`
4. Builds production assets with `npm run build`
5. Creates a zip file containing the built assets, named `build-assets-{commit-sha}.zip`
6. Publishes a GitHub Release tagged as `build-{commit-sha}` with the zip file attached

The releases are marked as pre-releases and contain:
- The built assets zip file
- Metadata about the build (branch, commit, author)

## Server Deployment

When you run `bin/update.sh` on the server:

1. The script pulls the latest code with `git pull`
2. Determines the current commit SHA
3. Downloads `build-assets-{commit-sha}.zip` from GitHub Releases
4. Extracts the assets to the web root (`/customers/9/a/3/goteborgkk.se/httpd.www/`)
5. Updates Laravel caches

**No GitHub secrets or credentials are required** - the releases are publicly accessible.

## Manual Deployment

The workflow can also be triggered manually:

1. Go to the **Actions** tab on GitHub
2. Select the "Build and Release Assets" workflow
3. Click **Run workflow**
4. Choose the branch and click **Run workflow**

This will create a new release with build assets for the selected branch.

## Rollback to Previous Version

To rollback to a previous version:

1. SSH into the server
2. Navigate to the application directory
3. Checkout the desired commit: `git checkout <commit-sha>`
4. Run the update script: `bin/update.sh`

The script will automatically download and deploy the build assets for that specific commit.

## Local Development

Build assets are now ignored by git (added to `.gitignore`). To build locally:

```bash
npm install
npm run build
```

The built files will appear in:
- `public/build/` - Vite build output containing versioned JavaScript, CSS, and other frontend assets, plus a manifest file for cache-busting

This directory is automatically excluded from version control.

## Backend Deployment

Backend code (PHP, Laravel) is deployed by running the update script on the server:

1. SSH into the server
2. Navigate to the application directory
3. Run `bin/update.sh` to:
   - Pull latest code
   - Download and deploy build assets for the current commit
   - Refresh Laravel caches
4. If Composer dependencies changed, run `php composer.phar install --no-dev`

## Troubleshooting

### Deployment Fails

Check the GitHub Actions logs:
1. Go to the **Actions** tab
2. Click on the failed workflow run
3. Review the logs for error messages

Common issues:
- Build errors (check npm build logs)
- Insufficient permissions to create releases

### Assets Not Updating on Server

1. Check that the workflow completed successfully in the Actions tab
2. Verify the release exists at `https://github.com/Krisell/gkk-anmalan/releases`
3. Check the commit SHA matches: `git rev-parse HEAD` on the server
4. Look for the release tagged `build-{commit-sha}`
5. Ensure the server has internet access to download from GitHub

### Build Fails

If `npm run build` fails:
1. Check the Actions logs for the specific error
2. Try building locally to reproduce the issue
3. Ensure `package.json` and `package-lock.json` are up to date

### Download Fails on Server

If the server can't download the release:
1. Check internet connectivity: `curl -I https://github.com`
2. Verify the release exists: Check the releases page on GitHub
3. The URL format should be: `https://github.com/Krisell/gkk-anmalan/releases/download/build-{commit-sha}/build-assets-{commit-sha}.zip`
4. Ensure `curl` is installed on the server
