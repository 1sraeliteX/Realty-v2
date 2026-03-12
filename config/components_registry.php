<?php

/**
 * Component Registry - Centralized component management
 * Prevents scattering by managing all dependencies in one place
 */

class ComponentRegistry {
    private static $components = [];
    private static $loaded = [];
    
    /**
     * Register a component with its dependencies
     */
    public static function register($name, $path, $dependencies = []) {
        self::$components[$name] = [
            'path' => $path,
            'dependencies' => $dependencies
        ];
    }
    
    /**
     * Load a component and its dependencies
     */
    public static function load($name) {
        if (isset(self::$loaded[$name])) {
            return true;
        }
        
        if (!isset(self::$components[$name])) {
            throw new Exception("Component '$name' not found");
        }
        
        $component = self::$components[$name];
        
        // Load dependencies first
        foreach ($component['dependencies'] as $dep) {
            self::load($dep);
        }
        
        // Load the component
        require_once $component['path'];
        self::$loaded[$name] = true;
        
        return true;
    }
    
    /**
     * Initialize all core components
     */
    public static function init() {
        // Register core components
        self::register('ui-components', __DIR__ . '/../components/UIComponents.php');
        self::register('theme-toggle', __DIR__ . '/../components/ThemeToggleComponent.php');
        self::register('attachment-component', __DIR__ . '/../components/AttachmentComponent.php', ['ui-components']);
        self::register('searchable-dropdown', __DIR__ . '/../components/SearchableDropdown.php', ['ui-components']);
        self::register('layout', __DIR__ . '/../views/layout.php', ['ui-components']);
        self::register('dashboard-layout', __DIR__ . '/../views/admin/dashboard_layout.php', ['ui-components']);
        self::register('admin.dashboard_layout', __DIR__ . '/../views/admin/dashboard_layout.php', ['ui-components']);
        
        // Auto-register view components
        self::registerViewComponents();
    }
    
    /**
     * Auto-register all view components
     */
    private static function registerViewComponents() {
        $viewsDir = __DIR__ . '/../views';
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
        
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $relativePath = str_replace([$viewsDir . '/', '.php'], '', $file->getPathname());
                $name = str_replace('/', '.', $relativePath);
                
                // Only register if it doesn't have layout dependencies
                if (!strpos($file->getPathname(), 'layout')) {
                    self::register($name, $file->getPathname(), ['ui-components']);
                }
            }
        }
    }
    
    /**
     * Get component info
     */
    public static function getInfo($name = null) {
        if ($name) {
            return self::$components[$name] ?? null;
        }
        return self::$components;
    }
}

// Auto-initialize
ComponentRegistry::init();
