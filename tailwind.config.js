import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    // Le mode sombre n'est pas encore activé dans les vues (aucune classe
    // "dark:" n'est utilisée), mais la stratégie "class" est posée dès
    // maintenant pour que le Design System puisse documenter les
    // équivalents sombres sans rien changer au rendu actuel.
    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                display: ['"Playfair Display"', ...defaultTheme.fontFamily.serif],
            },
            keyframes: {
                'fade-in-up': {
                    '0%': { opacity: '0', transform: 'translateY(8px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
            animation: {
                'fade-in-up': 'fade-in-up 0.4s ease-out both',
            },
            colors: {
                primary: {
                    50: '#fdf2f8',
                    100: '#fce7f3',
                    200: '#fbcfe8',
                    300: '#f9a8d4',
                    400: '#f472b6',
                    500: '#ec4899',
                    600: '#db2777',
                    700: '#be185d',
                    800: '#9d174d',
                    900: '#831843',
                },
                blush: {
                    50: '#fff7f8',
                    100: '#ffeef0',
                    200: '#ffdee3',
                },
                // Alias sémantiques du Design System : ils reprennent
                // exactement les teintes Tailwind (gray/emerald/amber/red/blue)
                // déjà utilisées partout dans le projet, sous un nom qui
                // documente leur rôle. Purement additif : aucune classe
                // existante (bg-emerald-600, text-gray-700, ...) n'est
                // modifiée ou retirée.
                secondary: {
                    50: '#f9fafb',
                    100: '#f3f4f6',
                    200: '#e5e7eb',
                    300: '#d1d5db',
                    400: '#9ca3af',
                    500: '#6b7280',
                    600: '#4b5563',
                    700: '#374151',
                    800: '#1f2937',
                    900: '#111827',
                },
                success: {
                    50: '#ecfdf5',
                    100: '#d1fae5',
                    200: '#a7f3d0',
                    300: '#6ee7b7',
                    400: '#34d399',
                    500: '#10b981',
                    600: '#059669',
                    700: '#047857',
                    800: '#065f46',
                    900: '#064e3b',
                },
                warning: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                },
                danger: {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#dc2626',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d',
                },
                info: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                },
            },
        },
    },

    plugins: [forms],
};
