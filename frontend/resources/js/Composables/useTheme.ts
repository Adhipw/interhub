import logger from '@/Lib/logger';
import { ref, onMounted } from 'vue';

const isDarkMode = ref(false);

export function useTheme() {
    const toggleDarkMode = () => {
        isDarkMode.value = !isDarkMode.value;
        logger.log('Dark mode toggled:', isDarkMode.value);
        
        if (isDarkMode.value) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    };

    const initTheme = () => {
        const savedTheme = localStorage.getItem('theme');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        
        if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
            isDarkMode.value = true;
            document.documentElement.classList.add('dark');
        } else {
            isDarkMode.value = false;
            document.documentElement.classList.remove('dark');
        }
        logger.log('Theme initialized:', isDarkMode.value);
    };

    return {
        isDarkMode,
        toggleDarkMode,
        initTheme
    };
}
