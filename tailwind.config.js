import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['Sora', ...defaultTheme.fontFamily.sans],
                mono: ['IBM Plex Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                teal: {
                    950: '#0B2626',
                    900: '#123C3C',
                    800: '#1A5252',
                    700: '#236B6B',
                },
                sand: {
                    50: '#FBF8F1',
                    100: '#F7F1E3',
                    200: '#EDE2CB',
                },
                gold: {
                    500: '#C9973F',
                    600: '#B5822E',
                    700: '#96691F',
                },
                clay: {
                    600: '#8C3A2B',
                    700: '#732E22',
                },
                ink: '#22201C',
            },
        },
    },
    plugins: [forms],
};
