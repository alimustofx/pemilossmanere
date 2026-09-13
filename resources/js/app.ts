import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';
import { configureEcho } from '@laravel/echo-vue';

configureEcho({
    broadcaster: 'reverb',
});

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

void createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),

    layout: (name) => {
        switch (true) {
            case name === 'Welcome':
                return null;

            case name === 'Landing':
                return null;

            case name === 'Pengumuman':
                return null;

            case name.startsWith('auth/'):
                return AuthLayout;

            case name.startsWith('settings/'):
                return [AppLayout, SettingsLayout];

            case name.startsWith('Vote/'):
                return null;

            case name.startsWith('Hasil'):
                return null;

            default:
                return AppLayout;
        }
    },

    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();
