/** @type {import('tailwindcss').Config} */
const defaultTheme = require('tailwindcss/defaultTheme');

const disabledCss = {
  'blockquote p:first-of-type::before': false,
  'blockquote p:last-of-type::after': false,
  pre: false,
  code: false,
  'pre code': false,
  'code::before': false,
  'code::after': false,
}

module.exports = {
  jit: true,
  darkMode: ['class', '[data-theme="dark"]'],
  content: [
    "./src/template/views/**/*.blade.php",
    "./src/process-docs.class.php",
  ],
  theme: {
    screens: {
      ...defaultTheme.screens,
      'tablet': '960px',
    },
    fontSize: {
      ...defaultTheme.fontSize,
      'xs': '.80rem',
    },
    extend: {
      colors: {
        'blue-gray': {
          50: '#eceff1',
          100: '#cfd8dc',
          200: '#b0bec5',
          300: '#90a4ae',
          400: '#78909c',
          500: '#607d8b',
          600: '#546e7a',
          700: '#455a64',
          800: '#37474f',
          900: '#263238',
        }
      },
      typography: {
        DEFAULT: { css: disabledCss },
        sm: { css: disabledCss },
        lg: { css: disabledCss },
        xl: { css: disabledCss },
        '2xl': { css: disabledCss },
      },
      fontFamily: {
        inter: ['Inter', ...defaultTheme.fontFamily.sans],
        mono: ['Fira Code', ...defaultTheme.fontFamily.mono],
        source: ['Source Sans Pro', ...defaultTheme.fontFamily.sans],
        'ubuntu-mono': ['Ubuntu Mono', ...defaultTheme.fontFamily.mono],
      },
      fontWeight: {
        'inherit': 'inherit',
      },
    },
  },
  plugins: [
    require('@tailwindcss/typography'),
  ],
}

