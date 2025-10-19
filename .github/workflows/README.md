# GitHub Actions Workflows

## Deploy Workflow

The `deploy.yml` workflow automatically builds and deploys frontend assets to the server when changes are pushed to the `master` or `main` branch.

### Required Secrets

The following secrets need to be configured in the GitHub repository settings (Settings → Secrets and variables → Actions):

- `FTP_SERVER`: The FTP server hostname (e.g., `ftp.example.com`)
- `FTP_USERNAME`: The FTP username for authentication
- `FTP_PASSWORD`: The FTP password for authentication

### What Gets Deployed

The workflow:
1. Checks out the code
2. Sets up Node.js 20
3. Installs npm dependencies
4. Runs `npm run build` to generate production assets
5. Deploys the `public/` directory contents to the server via FTP

The deployment excludes certain files (like `.htaccess`, `index.php`, etc.) that should not be overwritten on the server.

### Manual Trigger

The workflow can also be triggered manually from the Actions tab in GitHub if needed.
