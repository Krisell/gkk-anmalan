const mix = require('laravel-mix')

mix
  .webpackConfig({
    output: {
      chunkFilename: 'js/[name].js?id=[chunkhash]',
    },
  })
  .options({
    processCssUrls: false,
    terser: {
      extractComments: false,
    },
  })
  .js('resources/js/app.js', 'public/js')
  .postCss('resources/css/main.css', 'public/css', [require('tailwindcss')])
  .version()
