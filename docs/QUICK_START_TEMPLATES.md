# Quick Start Templates - Anti-Scattering Ready

## 🚀 Ready-to-Use Templates

Just copy these files and start building - no need to type `view` manually!

### 📁 Admin Pages Template
**File:** `views/admin/your_page.php`
```php
<?php
// Initialize framework
require_once __DIR__ . '/../../config/init_framework.php';

// Set page data
ViewManager::set('title', $title ?? 'Page Title');
ViewManager::set('pageTitle', $pageTitle ?? 'Page Title');

// Get data from provider
$properties = DataProvider::get('properties');
$tenants = DataProvider::get('tenants');

// Process data for this view
$pageData = [
  'properties' => $properties,
  'tenants' => $tenants,
  'stats' => [
    'total' => count($properties)
  ]
];

// Render view
echo ViewManager::render('admin.your_page', $pageData);
?>
```

### 📁 Regular Pages Template
**File:** `views/your_page.php`
```php
<?php
// Initialize framework
require_once __DIR__ . '/../config/init_framework.php';

// Set page data
ViewManager::set('title', $title ?? 'Page Title');
ViewManager::set('pageTitle', $pageTitle ?? 'Page Title');

// Get data from provider
$properties = DataProvider::get('properties');
$tenants = DataProvider::get('tenants');

// Process data for this view
$pageData = [
  'properties' => $properties,
  'tenants' => $tenants,
  'stats' => [
    'total' => count($properties)
  ]
];

// Render view
echo ViewManager::render('your_page', $pageData);
?>
```

### 📁 Controller Method Template
```php
public function yourMethod() {
  // Get data
  $data = DataProvider::get('data');
  
  // Process business logic
  $processedData = [
    'data' => $data
  ];
  
  // Set view data
  ViewManager::set('title', 'Page Title');
  
  // Render view
  return ViewManager::render('admin.your_view', $processedData);
}
```

### 📁 Component Class Template
```php
<?php

class YourComponent {
  
  /**
   * Render component
   */
  public static function render($data = []) {
    // Component logic here
    extract($data);
    
    return '<div class="your-component">
      . $content
      </div>';
  }
  
  /**
   * Process data
   */
  private static function processData($data) {
    // Data processing logic
    return $data;
  }
}
?>
```

## 🎯 How to Use

1. **Copy the template** you need
2. **Rename the file** to your page name
3. **Replace placeholder names** (your_page, your_method, etc.)
4. **Start building!** ✨

## 🚀 Super Fast Workflow

### For New Admin Pages:
1. Copy `views/admin/your_page.php` template
2. Rename to `views/admin/my_new_page.php`
3. Replace `your_page` with `my_new_page`
4. Done! 🎉

### For New Regular Pages:
1. Copy `views/your_page.php` template  
2. Rename to `views/my_new_page.php`
3. Replace `your_page` with `my_new_page`
4. Done! 🎉

### For New Components:
1. Copy component template
2. Rename class to `MyComponent`
3. Done! 🎉

## ✅ Benefits

- **No manual typing** - just copy & paste
- **Anti-scattering built-in** - follows all safe patterns
- **Consistent structure** - all pages same pattern
- **Fast development** - create pages in seconds

## 🎪 Ready Files Created

I've already created these for you:
- `views/admin/new_page_example.php` - Admin page template
- `views/new_view_example.php` - Regular page template

**Just copy these and rename!** 🚀
