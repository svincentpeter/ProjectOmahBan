/** @type {import('tailwindcss').Config} */
export default {
  darkMode: 'never', // Disable dark mode completely
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
    "./Modules/**/*.blade.php",
    "./node_modules/flowbite/**/*.js",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
      },
      colors: {
        ob: {
          primary: '#4f46e5', // indigo-600 (Updated from user design)
          soft: '#eef2ff',    // indigo-50
        },
        // Custom red for hover
        accent: {
          50: '#fef2f2',
          100: '#fee2e2',
          200: '#fecaca',
          300: '#fca5a5',
          400: '#f87171',
          500: '#ef4444',
          600: '#dc2626',
          700: '#b91c1c',
          800: '#991b1b',
          900: '#7f1d1d',
        },
      },
    },
  },
  plugins: [
    require('flowbite/plugin')({
      charts: true,
      datatables: true,
    }),
  ],
}
