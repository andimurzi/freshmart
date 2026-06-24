/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        leaf: {
          50:  '#f0faf4',
          100: '#d8f3e3',
          400: '#4eca7a',
          500: '#27a855',
          600: '#1e8f45',
          700: '#1a7a3b',
          800: '#155f2e',
        },
        cream: '#FAFAF5',
        ink:   '#1C1C1E',
        tomato:'#E84040',
      },
      fontFamily: {
        display: ['"Fraunces"', 'serif'],
        sans:    ['"DM Sans"', 'sans-serif'],
      },
      boxShadow: {
        card: '0 2px 16px 0 rgba(28,28,30,0.08)',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}