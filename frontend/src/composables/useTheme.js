import { ref, watch, onMounted } from 'vue'

// Global theme state
const isDarkMode = ref(false)

export function useTheme() {
  // Initialize theme from localStorage
  const initializeTheme = () => {
    const savedTheme = localStorage.getItem('darkMode')
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches

    isDarkMode.value = savedTheme ? savedTheme === 'true' : prefersDark
    applyTheme()
  }

  // Apply theme to document
  const applyTheme = () => {
    const html = document.documentElement
    const body = document.body

    if (isDarkMode.value) {
      html.classList.add('dark')
      body.classList.add('dark')
      body.style.backgroundColor = '#0f172a' // slate-900
      body.style.color = '#f1f5f9' // slate-100
    } else {
      html.classList.remove('dark')
      body.classList.remove('dark')
      body.style.backgroundColor = '#ffffff'
      body.style.color = '#1e293b' // slate-800
    }
  }

  // Toggle theme
  const toggleTheme = () => {
    isDarkMode.value = !isDarkMode.value
    localStorage.setItem('darkMode', isDarkMode.value.toString())
    applyTheme()
  }

  // Set specific theme
  const setTheme = (dark) => {
    isDarkMode.value = dark
    localStorage.setItem('darkMode', isDarkMode.value.toString())
    applyTheme()
  }

  // Watch for theme changes
  watch(isDarkMode, () => {
    applyTheme()
  })

  // Listen for system theme changes
  const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)')
  const handleSystemThemeChange = (e) => {
    if (!localStorage.getItem('darkMode')) {
      isDarkMode.value = e.matches
    }
  }

  onMounted(() => {
    initializeTheme()
    mediaQuery.addEventListener('change', handleSystemThemeChange)
  })

  return {
    isDarkMode,
    toggleTheme,
    setTheme,
    initializeTheme
  }
}

// Export for global access
export { isDarkMode }
