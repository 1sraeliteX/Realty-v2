<?php

/**
 * PropertyHelper - Utility class for property-related operations
 * Anti-scattering compliant - centralized helper functions
 */
class PropertyHelper {
    
    /**
     * Returns a web-accessible image src for a property.
     * Handles JSON images array, missing files, and provides fallback.
     * 
     * @param string|array|null $imagesData - JSON string or array of image filenames
     * @param string $fallbackPath - Custom fallback image path
     * @return string - Web-accessible image URL
     */
    public static function getImageSrc($imagesData = null, string $fallbackPath = '/assets/images/property-placeholder.svg'): string {
        // Default fallback
        if (empty($imagesData)) {
            return $fallbackPath;
        }
        
        // Parse JSON if it's a string
        $imagesArray = is_string($imagesData) ? json_decode($imagesData, true) : $imagesData;
        
        // Validate parsed data
        if (!is_array($imagesArray) || empty($imagesArray)) {
            return $fallbackPath;
        }
        
        // Get first image from array
        $firstImage = $imagesArray[0] ?? null;
        if (empty($firstImage)) {
            return $fallbackPath;
        }
        
        // Try different path combinations to find the actual file
        $possiblePaths = [
            // Direct path (if stored with prefix)
            '/' . ltrim($firstImage, '/'),
            
            // Uploads/properties path (most common)
            '/uploads/properties/' . basename($firstImage),
            
            // Public uploads path
            '/public/uploads/properties/' . basename($firstImage),
            
            // Assets path
            '/assets/images/properties/' . basename($firstImage),
        ];
        
        // Check each possible path
        foreach ($possiblePaths as $path) {
            if (self::fileExistsInWebRoot($path)) {
                return $path;
            }
        }
        
        // If no file found, return fallback
        return $fallbackPath;
    }
    
    /**
     * Check if a file exists in the web root directory
     * 
     * @param string $relativePath - Relative path from web root
     * @return bool
     */
    private static function fileExistsInWebRoot(string $relativePath): bool {
        // Remove leading slash for file system path
        $relativePath = ltrim($relativePath, '/');
        
        // Get document root (adjust for different server configs)
        $documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? $_SERVER['CONTEXT_DOCUMENT_ROOT'] ?? '';
        
        // If document root is empty, try common paths
        if (empty($documentRoot)) {
            // Try to detect the public directory
            $possibleRoots = [
                __DIR__ . '/../../public',  // Standard structure
                __DIR__ . '/../../../public', // Deeper structure
                dirname(__DIR__) . '/public', // Alternative
            ];
            
            foreach ($possibleRoots as $root) {
                $fullPath = $root . '/' . $relativePath;
                if (file_exists($fullPath) && is_file($fullPath)) {
                    return true;
                }
            }
            return false;
        }
        
        // Standard document root check
        $fullPath = $documentRoot . '/' . $relativePath;
        return file_exists($fullPath) && is_file($fullPath);
    }
    
    /**
     * Get all images for a property as an array of valid URLs
     * 
     * @param string|array|null $imagesData
     * @param string $fallbackPath
     * @return array
     */
    public static function getAllImageSrc($imagesData = null, string $fallbackPath = '/assets/images/property-placeholder.svg'): array {
        $result = [];
        
        if (empty($imagesData)) {
            return [$fallbackPath];
        }
        
        $imagesArray = is_string($imagesData) ? json_decode($imagesData, true) : $imagesData;
        
        if (!is_array($imagesArray) || empty($imagesArray)) {
            return [$fallbackPath];
        }
        
        foreach ($imagesArray as $image) {
            if (empty($image)) continue;
            
            $possiblePaths = [
                '/' . ltrim($image, '/'),
                '/uploads/properties/' . basename($image),
                '/public/uploads/properties/' . basename($image),
                '/assets/images/properties/' . basename($image),
            ];
            
            foreach ($possiblePaths as $path) {
                if (self::fileExistsInWebRoot($path)) {
                    $result[] = $path;
                    break; // Stop checking other paths for this image once found
                }
            }
        }
        
        return !empty($result) ? $result : [$fallbackPath];
    }
    
    /**
     * Format property status for display
     * 
     * @param string|null $status
     * @return string
     */
    public static function formatStatus(?string $status): string {
        $statusMap = [
            'active' => 'Available',
            'inactive' => 'Inactive',
            'maintenance' => 'Under Maintenance',
            'occupied' => 'Occupied',
            'available' => 'Available',
            'rented' => 'Rented',
            'pending' => 'Pending',
        ];
        
        return $statusMap[$status] ?? ucfirst($status ?? 'Unknown');
    }
    
    /**
     * Get status badge color class
     * 
     * @param string|null $status
     * @return string
     */
    public static function getStatusBadgeClass(?string $status): string {
        $colorMap = [
            'active' => 'success',
            'available' => 'success',
            'inactive' => 'secondary',
            'maintenance' => 'warning',
            'occupied' => 'info',
            'rented' => 'info',
            'pending' => 'warning',
        ];
        
        return $colorMap[$status] ?? 'secondary';
    }
}
