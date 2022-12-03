const childProcess = require('child_process');

async function globalSetup(config) {
    childProcess.execSync('e2e/before.sh', { stdio: 'inherit' });
    childProcess.execSync('e2e/server.sh', { stdio: 'inherit' });

    return () => childProcess.execSync('e2e/after.sh', { stdio: 'inherit' });
}

export default globalSetup;