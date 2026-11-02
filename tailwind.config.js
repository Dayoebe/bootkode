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
      colors: {
        action: { DEFAULT: 'rgb(var(--bk-action) / <alpha-value>)', foreground: 'rgb(var(--bk-action-text) / <alpha-value>)' },
        brand: { DEFAULT: 'rgb(var(--accent-primary) / <alpha-value>)', ink: '#0f172a', mint: '#8fe388' },
        surface: { DEFAULT: 'rgb(var(--bg-secondary) / <alpha-value>)', muted: 'rgb(var(--bg-primary) / <alpha-value>)' },
        content: { DEFAULT: 'rgb(var(--text-primary) / <alpha-value>)', muted: 'rgb(var(--text-secondary) / <alpha-value>)' },
        line: 'rgb(var(--border-primary) / <alpha-value>)',
      },
      fontFamily: {
        sans: ['Manrope', 'ui-sans-serif', 'system-ui', 'sans-serif'],
        display: ['Sora', 'Manrope', 'sans-serif'],
      },
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
  ],
};
