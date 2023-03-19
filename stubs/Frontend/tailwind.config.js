/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['resources/views/**/*.blade.php', 'resources/js/**/*.js', 'resources/css/**/*.css'],
  theme: {
    extend: {},
  },
  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
}
