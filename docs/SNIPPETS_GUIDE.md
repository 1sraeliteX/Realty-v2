# VS Code Snippets Guide - Anti-Scattering Architecture

## 🚀 How to Use

Type the prefix and press **Tab** to expand snippets:

### Core Patterns

| Prefix | What it does | Example |
|--------|--------------|---------|
| `view` | Creates isolated view template | `view` → Full view template |
| `component` | Loads component safely | `component` → `ComponentRegistry::load()` |
| `data` | Gets data from provider | `data` → `DataProvider::get()` |
| `setdata` | Sets data in provider | `setdata` → `DataProvider::set()` |
| `render` | Renders view cleanly | `render` → `ViewManager::render()` |
| `uicomp` | Uses UI component | `uicomp` → `UIComponents::method()` |

### Advanced Patterns

| Prefix | What it does |
|--------|--------------|
| `controller` | Creates clean controller method |
| `isoclass` | Creates isolated component class |
| `init` | Initializes framework |
| `setview` | Sets view manager data |
| `register` | Registers component |

## 📝 Quick Examples

### Create a New View
Type: `view` + Tab
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
echo ViewManager::render('admin.view_name', $pageData);
?>
```

### Load Component
Type: `component` + Tab
```php
// Load component
ComponentRegistry::load('ui-components');
```

### Get Data
Type: `data` + Tab
```php
DataProvider::get('properties', 'default_value')
```

### Render View
Type: `render` + Tab
```php
ViewManager::render('admin.dashboard', $data)
```

## 🎯 Best Practices

1. **Always start with `init`** - Initialize framework first
2. **Use `view` for new pages** - Follows anti-scattering pattern
3. **Use `data` instead of mock data** - Centralized data management
4. **Use `component` for dependencies** - Safe component loading
5. **Use `render` for output** - Clean view rendering

## ⚡ Speed Tips

- `view` + Tab = Complete isolated view
- `data` + Tab = Get any data
- `render` + Tab = Render anything
- `uicomp` + Tab = Use UI components

## 🔧 Setup Complete

Your VS Code now has:
- ✅ Auto-expanding snippets for all patterns
- ✅ PHP validation on save
- ✅ Tab completion enabled
- ✅ Framework shortcuts built-in

**Just type the prefix and press Tab!**
