module.exports = {
  content: ['./resources/**/*.vue', './resources/views/**/*.blade.php'],
  theme: {
    extend: {
      colors: {
        gkk: '#314270',
        'gkk-light': '#45557e',
        'gkk-lightest': '#a6acb9',
      },
    },
  },
  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
}
