import { onMounted, ref, watch } from 'vue';

export function useThemeMode() {
    const isDarkMode = ref(localStorage.getItem('appearance'));

    // Check for system preference on dark mode
    onMounted(() => {
        // Check for saved preference in localStorage
        const savedMode = localStorage.getItem('appearance');

        if (savedMode !== null) {
            isDarkMode.value = savedMode === 'dark';
        }

        // Listen for changes in system preference
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (event) => {
            // Only update if user hasn't set a preference
            if (localStorage.getItem('appearance') === null) {
                isDarkMode.value = event.matches;
            }
        });
    });

    // Toggle dark mode
    const toggleDarkMode = () => {
        isDarkMode.value = !isDarkMode.value;
        // Save preference to localStorage
        localStorage.setItem('darkMode', isDarkMode.value.toString());
    };

    // Watch for changes and update document classes if needed
    watch(
        isDarkMode,
        (newValue) => {
            if (newValue) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        },
        { immediate: true },
    );

    return {
        isDarkMode,
        toggleDarkMode,
    };
}
