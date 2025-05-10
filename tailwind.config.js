/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'primary': '#1E90FF',
        'background': '#1C2526',
        'highlight': '#0A3D62',
      },
    },
  },
  plugins: [],
} 