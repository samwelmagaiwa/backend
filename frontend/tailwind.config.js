/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ['./public/index.html', './src/**/*.{vue,js,ts,jsx,tsx}'],
  theme: {
    extend: {
      colors: {
        primary: '#1E40AF' // Blue-700
      },
      fontFamily: {
        serif: ['Georgia', 'serif']
      },
      screens: {
        xs: '475px',
        '3xl': '1600px'
      },
      spacing: {
        18: '4.5rem',
        88: '22rem'
      },
      maxWidth: {
        '8xl': '88rem',
        '9xl': '96rem'
      }
    }
  },
  plugins: [],

  // Optimization settings
  corePlugins: {
    // Disable unused core plugins to reduce bundle size
    preflight: true,
    container: false // We use custom responsive containers
  },

  // Safelist important classes that might be generated dynamically
  safelist: [
    // Dynamic color classes
    'bg-green-600',
    'bg-red-600',
    'bg-yellow-600',
    'bg-blue-600',
    'text-green-600',
    'text-red-600',
    'text-yellow-600',
    'text-blue-600',
    // Animation classes
    'animate-spin',
    'animate-pulse',
    'animate-bounce',
    // Transform classes that might be used dynamically
    'scale-95',
    'scale-100',
    'scale-105',
    'translate-x-0',
    'translate-x-full',
    '-translate-x-full'
  ]
}
