import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue2'
import { resolve } from 'path'
import { homedir } from 'os'
import fs from 'fs'

export default defineConfig({
  plugins: [
    laravel({
      input: ['resources/css/main.css', 'resources/js/app.js'],
      refresh: true,
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
  ],
  server: detectServerConfig('anmalan.test'),
})

// We'll have to figure out a better way to run the client dev server, since not everyone uses Valet
// Maybe the dev server can have it's own ssl certs?
function detectServerConfig(host) {
  const keyPath = resolve(homedir(), `.config/valet/Certificates/${host}.key`)
  const certificatePath = resolve(homedir(), `.config/valet/Certificates/${host}.crt`)

  if (!fs.existsSync(keyPath)) {
    return {}
  }

  if (!fs.existsSync(certificatePath)) {
    return {}
  }

  return {
    hmr: { host },
    host,
    https: {
      key: fs.readFileSync(keyPath),
      cert: fs.readFileSync(certificatePath),
    },
  }
}
