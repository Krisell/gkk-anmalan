module.exports = {
  future: {
    removeDeprecatedGapUtilities: true,
  },
  content: ['./resources/**/*.html', './resources/**/*.vue', './resources/**/*.jsx', './resources/**/*.php'],
  theme: {
    extend: {
      colors: {
        gkk: '#314270',
        'gkk-light': '#45557e',
        'gkk-lightest': '#a6acb9',
      },
    },
  },
  plugins: [require('@tailwindcss/forms')],
}
