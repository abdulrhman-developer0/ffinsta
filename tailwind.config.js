import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class', // Toggle via .dark class on <html>

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Cairo', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Brand primary
                brand: {
                    50:  '#f0f4ff',
                    100: '#dce8ff',
                    200: '#b9d0ff',
                    300: '#85adff',
                    400: '#4d80ff',
                    500: '#2055f5',
                    600: '#1140e8',
                    700: '#0e30c4',
                    800: '#0f2a9e',
                    900: '#132980',
                    950: '#0d1a52',
                },
                // Dark mode surfaces
                dark: {
                    50:  '#f8fafc',
                    100: '#f1f5f9',
                    700: '#1e293b',
                    800: '#0f172a',
                    850: '#0a1020',
                    900: '#060d1a',
                },
            },
            boxShadow: {
                'glow': '0 0 20px rgba(32, 85, 245, 0.3)',
                'card': '0 4px 24px rgba(0, 0, 0, 0.08)',
                'card-dark': '0 4px 24px rgba(0, 0, 0, 0.4)',
            },
            animation: {
                'fade-in': 'fadeIn 0.2s ease-out',
                'slide-up': 'slideUp 0.3s ease-out',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(8px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },

    plugins: [forms],
};
