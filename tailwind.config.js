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
        'secondary': '#FFFFFF',
        'background': '#1C2526',
        'accent': '#E5E7EB',
        'highlight': '#0A3D62',
        'gray-750': '#2D3748',
      },
      fontFamily: {
        'sans': ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
      },
      boxShadow: {
        'card': '0 4px 6px rgba(0, 0, 0, 0.1)',
      },
      borderRadius: {
        'xl': '0.75rem',
      },
    },
  },
  plugins: [],
} 