<?php

/**
 * View Manager - Centralized view rendering
 * Prevents scattering by managing all view rendering logic
 */

class ViewManager {
    private static $data = [];
    private static $currentLayout = null;
    
    /**
     * Set global view data
     */
    public static function set($key, $value) {
        self::$data[$key] = $value;
    }
    
    /**
     * Get global view data
     */
    public static function get($key, $default = null) {
        return self::$data[$key] ?? $default;
    }
    
    /**
     * Set layout
     */
    public static function setLayout($layout) {
        self::$currentLayout = $layout;
    }
    
    /**
     * Render a view with automatic component loading
     */
    public static function render($view, $data = [], $layout = null) {
        // Merge global data with local data
        $viewData = array_merge(self::$data, $data);
        
        // Extract data to make variables available in view
        extract($viewData);
        
        // Load required components
        ComponentRegistry::load('ui-components');
        
        // Capture view content
        $content = self::captureView($view, $viewData);
        
        // Apply layout if specified
        if ($layout || self::$currentLayout) {
            $layoutFile = $layout ?? self::$currentLayout;
            return self::applyLayout($layoutFile, $content, $viewData);
        }
        
        return $content;
    }
    
    /**
     * Capture view output
     */
    private static function captureView($view, $data) {
        $viewPath = self::resolveViewPath($view);
        
        if (!file_exists($viewPath)) {
            throw new Exception("View '$view' not found at: $viewPath");
        }
        
        ob_start();
        include $viewPath;
        return ob_get_clean();
    }
    
    /**
     * Apply layout to content
     */
    private static function applyLayout($layout, $content, $data) {
        $layoutPath = self::resolveLayoutPath($layout);
        
        if (!file_exists($layoutPath)) {
            throw new Exception("Layout '$layout' not found at: $layoutPath");
        }
        
        // Set content for layout
        $data['content'] = $content;
        extract($data);
        
        ob_start();
        include $layoutPath;
        return ob_get_clean();
    }
    
    /**
     * Resolve view path
     */
    private static function resolveViewPath($view) {
        // Convert dot notation to path
        $view = str_replace('.', '/', $view);
        
        // Check for .php extension
        if (!preg_match('/\.php$/', $view)) {
            $view .= '.php';
        }
        
        return __DIR__ . "/../views/{$view}";
    }
    
    /**
     * Resolve layout path
     */
    private static function resolveLayoutPath($layout) {
        // Convert dot notation to path
        $layout = str_replace('.', '/', $layout);
        
        // Check for .php extension
        if (!preg_match('/\.php$/', $layout)) {
            $layout .= '.php';
        }
        
        return __DIR__ . "/../views/{$layout}";
    }
    
    /**
     * Render component
     */
    public static function component($name, $data = []) {
        ComponentRegistry::load($name);
        
        $component = ComponentRegistry::getInfo($name);
        if (!$component) {
            throw new Exception("Component '$name' not found");
        }
        
        extract($data);
        ob_start();
        include $component['path'];
        return ob_get_clean();
    }
    
    /**
     * Clear all data
     */
    public static function clear() {
        self::$data = [];
        self::$currentLayout = null;
    }
}
