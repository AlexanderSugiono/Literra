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
                serif: ['Playfair Display', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                navy: {
                    DEFAULT: '#0A192F',
                    50: '#E8EDF5',
                    100: '#C5D1E5',
                    200: '#9DB0CF',
                    300: '#708BB5',
                    400: '#4A6A9A',
                    500: '#0A192F',
                    600: '#091627',
                    700: '#07121F',
                    800: '#060E18',
                    900: '#040A11',
                },
                gold: {
                    DEFAULT: '#FED65B',
                    50: '#FFF9E5',
                    100: '#FEF0BF',
                    200: '#FEE695',
                    300: '#FEDD6B',
                    400: '#FED65B',
                    500: '#FED65B',
                    600: '#D9B43D',
                    700: '#B3922A',
                    800: '#8D721E',
                    900: '#675314',
                },
                offwhite: '#F9F9FF',
                graytext: '#44474D',
            },
            borderRadius: {
                DEFAULT: '0.25rem',
                lg: '0.5rem',
                xl: '0.75rem',
            },
        },
    },

    plugins: [forms],
};
