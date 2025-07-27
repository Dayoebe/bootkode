/** @type {import('tailwindcss').Config} */
module.exports = {
    plugins: [
        require('tailwindcss-email-variants'),
        require('@tailwindcss/typography'),
      ],
    darkMode: "class",
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {},
    },
    plugins: [],
};
