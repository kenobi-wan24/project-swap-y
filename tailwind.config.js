/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        // SWAPY brand colors — use as: bg-primary, text-primary, border-primary, etc.
        primary:   '#ED730C',   // orange
        secondary: '#149189',   // teal
        heading:   '#3A3330',   // dark brown — headings & body text
      },
      fontFamily: {
        // DM Sans loaded via Google Fonts in guest.blade.php
        sans: ['DM Sans', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
    },
  },
  plugins: [],
}