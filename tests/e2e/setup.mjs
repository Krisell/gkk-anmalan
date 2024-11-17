import childProcess from 'child_process'

const PORT = 65456

function exec(cmd, env = {}) {
    // console.log('\x1b[36m%s\x1b[0m', `> ${cmd}`)
    childProcess.execSync(cmd, { stdio: 'inherit', env: { ...env, ...process.env } })
}

export default async function globalSetup(config) {
    exec('php artisan route:cache', { APP_ENV: 'testing' })
    exec('touch database/e2e.sqlite')
    exec('php artisan migrate:fresh --database=sqlite-e2e')
    exec(`php artisan serve --port=${PORT} > /dev/null &`, {
        SESSION_SECURE_COOKIE: false, // Allow app to run on non-https
        DB_CONNECTION: 'sqlite-e2e',
        TELESCOPE_ENABLED: false,
        DEBUGBAR_ENABLED: false,
        MAIL_MAILER: 'array',
        APP_ENV: 'testing',
        BCRYPT_ROUNDS: 4,
    })

    return () => {
        exec('php artisan route:clear')

        // Kill any already running server on specified port
        exec(`ALREADY_RUNNING_SERVERS=$(lsof -t -i:${PORT})
echo $ALREADY_RUNNING_SERVERS
if [ ! -z "$ALREADY_RUNNING_SERVERS" ]
then
    echo "Killing server"
    kill "$ALREADY_RUNNING_SERVERS"
fi`)
    }
}
