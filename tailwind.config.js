const colors = require('tailwindcss/colors')

const vatukLightBlue = {
    DEFAULT: '#25ADE3',
    '50': '#8dd8f6',
    '100': '#78cdf1',
    '200': '#62c3ec',
    '300': '#006192',
    '400': '#48b8e8',
    '500': '#007aab',
    '600': '#007aab',
    '700': '#005888',
    '800': '#00325e',
    '900': '#000000'
}

const vatukDarkBlue = {
    DEFAULT: '#17375E',
    '50': '#3378cd',
    '100': '#2c6ab6',
    '200': '#265d9f',
    '300': '#215089',
    '400': '#1c4373',
    '500': '#17375E',
    '600': '#022b50',
    '700': '#001f43',
    '800': '#001436',
    '900': '#000000'
}

module.exports = {
    content: [
        './resources/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                'vatuk-darkblue': vatukDarkBlue,
                'vatuk-lightblue': vatukLightBlue,
                danger: colors.rose,
                primary: vatukLightBlue,
                success: colors.green,
                warning: colors.amber,
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
}
