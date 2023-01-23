const defaultTheme = require('tailwindcss/defaultTheme')
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
      screens: {
          'xs': '475px',
          ...defaultTheme.screens,
      },
        extend: {},
  },
  plugins: [
      require('@tailwindcss/line-clamp')
  ],
}
