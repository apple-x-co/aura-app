/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./tailwindcss/**/*.{html,js}"],
  theme: {
    extend: {},
  },
  plugins: [
      require('@tailwindcss/forms'),
  ],
}

