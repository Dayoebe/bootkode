/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: "class",
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Outfit', 'sans-serif'], // Outfit is now default font
      },
    },
  },
  plugins: [
    require('tailwindcss-email-variants'),
    require('@tailwindcss/typography'),
  ],
};
