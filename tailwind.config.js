module.exports = {
  purge: [],
  theme: {
    extend: {
      colors: {
        gkk: '#314270',
        'gkk-light': '#45557e',
        'gkk-lightest': '#a6acb9',
      }
    },
  },
  variants: {},
  plugins: [
    require('@tailwindcss/ui'),
  ],
}
