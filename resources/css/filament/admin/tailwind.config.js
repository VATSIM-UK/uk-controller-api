import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

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

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                'vatuk-darkblue': vatukDarkBlue,
                'vatuk-lightblue': vatukLightBlue,
            },
        },
    },
}
