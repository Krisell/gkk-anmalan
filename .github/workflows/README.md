# GitHub Actions Workflows

## Build and Release Assets Workflow

The `deploy.yml` workflow automatically builds frontend assets and publishes them as GitHub Releases when changes are pushed to the `master` or `main` branch.

### How It Works

The workflow:
1. Checks out the code
2. Sets up Node.js 20
3. Installs npm dependencies
4. Runs `npm run build` to generate production assets
5. Creates a zip file named `build-assets-{commit-sha}.zip` containing the `public/build/` directory
6. Creates a GitHub Release tagged as `build-{commit-sha}` with the zip file attached

### What Gets Published

Each release contains:
- A zip file with the built frontend assets (`public/build/` directory)
- The full commit SHA in the tag and filename
- Metadata about the build (branch, commit, author)

### Server Deployment

The server deployment script (`bin/update.sh`) automatically:
1. Pulls the latest code
2. Downloads the build assets for the current commit from GitHub Releases
3. Extracts them to the web root
4. Updates Laravel caches

No manual intervention or GitHub secrets are required.

### Manual Trigger

The workflow can be triggered manually from the Actions tab in GitHub if needed.

### Rollback

To rollback to a previous version:
1. On the server, checkout the desired commit: `git checkout <commit-sha>`
2. Run the update script: `bin/update.sh`
3. The script will automatically download the correct build assets for that commit
