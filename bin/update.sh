#!/usr/bin/bash

# Get the current git commit hash
COMMIT_SHA=$(git rev-parse HEAD)
ASSETS_URL="https://github.com/Krisell/gkk-anmalan/releases/download/build-${COMMIT_SHA}/build-assets-${COMMIT_SHA}.zip"
TEMP_DIR="/tmp/gkk-build-assets"

echo "Updating to commit: ${COMMIT_SHA}"

# Pull latest code
git pull

# Download and extract build assets
echo "Downloading build assets from GitHub..."
mkdir -p "${TEMP_DIR}"
cd "${TEMP_DIR}"

# Download the build assets
if curl -L -f -o "build-assets.zip" "${ASSETS_URL}"; then
    echo "Build assets downloaded successfully"
    
    # Extract to the web root
    unzip -o "build-assets.zip" -d /customers/9/a/3/goteborgkk.se/httpd.www/
    
    echo "Build assets deployed successfully"
    
    # Cleanup
    rm -rf "${TEMP_DIR}"
else
    echo "Warning: Could not download build assets for commit ${COMMIT_SHA}"
    echo "The build may still be in progress or the release may not exist yet."
    echo "You can check: ${ASSETS_URL}"
fi

# Return to project directory
cd /customers/9/a/3/goteborgkk.se/httpd.private/web

# Cache Laravel configuration
php /customers/9/a/3/goteborgkk.se/httpd.private/web/artisan config:cache
php /customers/9/a/3/goteborgkk.se/httpd.private/web/artisan route:cache

echo "Update complete!"
