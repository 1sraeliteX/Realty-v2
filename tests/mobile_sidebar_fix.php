<?php

/**
 * Mobile Sidebar Fix - Anti-Scattering Compliant
 * Fixes mobile viewport overflow for admin profile section
 */

// Initialize framework first (anti-scattering requirement)
require_once __DIR__ . '/config/init_framework.php';

// Load required components through registry
ComponentRegistry::load('ui-components');

// Get data from centralized provider (anti-scattering compliant)
$currentUser = DataProvider::get('user');
$currentNotifications = DataProvider::get('notifications');

// Set data through ViewManager (anti-scattering compliant)
ViewManager::set('user', $currentUser);
ViewManager::set('notifications', $currentNotifications);

// CSS fix for mobile sidebar overflow
$mobileFixCSS = '
<style>
/* Mobile sidebar overflow fix */
@media (max-width: 1023px) {
    #sidebar {
        height: 100vh;
        max-height: 100vh;
        overflow-y: auto;
        overscroll-behavior: contain;
    }
    
    #sidebar .flex-col {
        min-height: 100vh;
    }
    
    #sidebar nav {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    #sidebar .border-t {
        flex-shrink: 0;
        position: sticky;
        bottom: 0;
        background: inherit;
    }
}

/* Ensure proper scrolling on iOS */
@supports (-webkit-touch-callout: none) {
    #sidebar {
        -webkit-overflow-scrolling: touch;
    }
}
</style>
';

// Set CSS through ViewManager
ViewManager::set('mobileFixCSS', $mobileFixCSS);

// JavaScript fix for mobile viewport
$mobileFixJS = '
<script>
// Mobile sidebar viewport fix
function fixMobileSidebarViewport() {
    const sidebar = document.getElementById("sidebar");
    const isMobile = window.innerWidth < 1024;
    
    if (isMobile && sidebar) {
        // Ensure full viewport height on mobile
        sidebar.style.height = "100vh";
        sidebar.style.maxHeight = "100vh";
        
        // Handle viewport changes on iOS Safari
        const handleViewportChange = () => {
            const vh = window.innerHeight * 0.01;
            document.documentElement.style.setProperty("--vh", `${vh}px`);
            sidebar.style.height = "calc(var(--vh, 1vh) * 100)";
        };
        
        // Initial setup
        handleViewportChange();
        
        // Listen for orientation changes and viewport adjustments
        window.addEventListener("resize", handleViewportChange);
        window.addEventListener("orientationchange", handleViewportChange);
    }
}

// Initialize when DOM is ready
document.addEventListener("DOMContentLoaded", fixMobileSidebarViewport);
</script>
';

// Set JavaScript through ViewManager
ViewManager::set('mobileFixJS', $mobileFixJS);

// Output the fixes
echo ViewManager::get('mobileFixCSS');
echo ViewManager::get('mobileFixJS');
?>
