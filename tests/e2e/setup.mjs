import childProcess from 'child_process'

function exec(cmd, env = {}) {
    // console.log('\x1b[36m%s\x1b[0m', `> ${cmd}`)
    childProcess.execSync(cmd, { stdio: 'inherit', env: { ...env, ...process.env } })
}

export default async function globalSetup() {
    exec('touch database/e2e.sqlite')
    exec('php artisan migrate:fresh --database=sqlite-e2e')
}
