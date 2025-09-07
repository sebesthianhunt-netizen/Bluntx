import type { Config } from 'tailwindcss'

export default {
  content: [
    './index.html',
    './src/**/*.{ts,tsx}',
  ],
  theme: {
    extend: {
      colors: {
        background: '#0A0A0B',
        card: '#121214',
        primary: '#00FF66',
        danger: '#FF3B3B',
        accent: '#FFB86B',
        gold: '#FFD700',
        text: {
          DEFAULT: '#FFFFFF',
          muted: '#B3B3B3'
        }
      }
    }
  },
  plugins: []
} satisfies Config
