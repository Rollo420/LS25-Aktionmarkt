import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/scss/**/*.scss',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            screens: {
                '3xl': '1920px', // Full HD
                '4xl': '2560px', // 4K
                '5xl': '3840px', // Ultra HD
            },
        },
    },
    plugins: [],
};
