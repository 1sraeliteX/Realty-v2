<?php
// Anti-scattering compliant theme toggle component
// No direct dependencies - self-contained component

/**
 * Theme Toggle Component
 * Renders a reusable dark/light theme toggle button with Font Awesome icons
 * Uses localStorage for theme persistence, defaults to dark theme
 */
class ThemeToggleComponent {
    
    /**
     * Render the theme toggle button
     * @param array $props Optional properties (size, class, etc.)
     * @return string HTML markup for the theme toggle
     */
    public static function render($props = []) {
        $size = $props['size'] ?? 'text-lg';
        $class = $props['class'] ?? 'text-gray-500 hover:text-gray-600 dark:hover:text-gray-300';
        $id = $props['id'] ?? 'theme-toggle-btn';
        
        return '
        <button 
            id="' . $id . '" 
            onclick="toggleTheme()" 
            class="' . $class . ' ' . $size . ' transition-colors duration-200"
            aria-label="Toggle dark mode"
        >
            <i class="fas fa-moon dark:hidden"></i>
            <i class="fas fa-sun hidden dark:block"></i>
        </button>
        ';
    }
    
    /**
     * Render the JavaScript for theme functionality
     * Should be included once per page
     * @return string JavaScript code
     */
    public static function renderScript() {
        return '
        <script>
            // Theme toggle function - updates both DOM and localStorage atomically
            function toggleTheme() {
                const html = document.documentElement;
                const isDark = html.classList.contains("dark");
                
                if (isDark) {
                    html.classList.remove("dark");
                    localStorage.setItem("theme", "light");
                } else {
                    html.classList.add("dark");
                    localStorage.setItem("theme", "dark");
                }
                
                // Dispatch custom event for components that need to react to theme changes
                window.dispatchEvent(new CustomEvent("themechange", {
                    detail: { isDark: !isDark }
                }));
            }
            
            // Initialize theme on page load (this runs after the blocking script)
            document.addEventListener("DOMContentLoaded", function() {
                // The blocking script already set the correct theme
                // This ensures consistency for any dynamic content
                const savedTheme = localStorage.getItem("theme");
                if (savedTheme === "dark") {
                    document.documentElement.classList.add("dark");
                } else if (savedTheme === "light") {
                    document.documentElement.classList.remove("dark");
                }
            });
        </script>';
    }
}
?>
