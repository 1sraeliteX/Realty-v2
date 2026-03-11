# Anti-Scattering Guide - Stop Components From Breaking

## The Problem
When you build one component, others break. This happens because:
- **Tight Coupling**: Everything depends on everything else
- **Global State**: Changes in one place affect others
- **Mixed Responsibilities**: Views doing data processing, controllers doing HTML
- **No Dependency Management**: Components loaded in random order

## The Solution: Component Isolation Architecture

### 1. Use the Component Registry
```php
// OLD WAY (causes scattering)
require_once __DIR__ . '/../../components/UIComponents.php';

// NEW WAY (isolated)
ComponentRegistry::load('ui-components');
```

### 2. Centralize Data Management
```php
// OLD WAY (scatters data everywhere)
$properties = [
    ['name' => 'Property 1', ...],
    ['name' => 'Property 2', ...]
];

// NEW WAY (centralized)
$properties = DataProvider::get('properties');
```

### 3. Use View Manager for Rendering
```php
// OLD WAY (mixed responsibilities)
require_once __DIR__ . '/../layout.php';
$content = ob_get_clean();
include $layout;

// NEW WAY (clean separation)
echo ViewManager::render('admin.dashboard', $data);
```

## Migration Steps

### Step 1: Initialize Framework
In your bootstrap/index.php:
```php
require_once __DIR__ . '/config/init_framework.php';
```

### Step 2: Update Existing Views
Replace this pattern:
```php
<?php
require_once __DIR__ . '/../../components/UIComponents.php';

$title = 'My Page';
$user = ['name' => 'John', 'email' => 'john@example.com'];
?>
<!-- HTML content -->
<?php include __DIR__ . '/../layout.php'; ?>
```

With this:
```php
<?php
require_once __DIR__ . '/../../config/init_framework.php';

ViewManager::set('title', 'My Page');
ViewManager::set('user', DataProvider::get('user'));

echo ViewManager::render('my_view', $data);
?>
```

### Step 3: Isolate Components
Each component should be self-contained:
```php
// components/MyComponent.php
class MyComponent {
    public static function render($data) {
        // Component logic here
        return '<div>' . $data['content'] . '</div>';
    }
}
```

## Best Practices

### ✅ DO:
- Use the Component Registry for all dependencies
- Keep data in DataProvider
- Use ViewManager for rendering
- Make components self-contained
- Keep views focused on presentation
- Use dependency injection

### ❌ DON'T:
- Include components directly in views
- Create mock data in views
- Mix business logic with presentation
- Modify global state in views
- Include layout files manually
- Use require_once for dependencies

## Testing Your Isolation

After implementing the new architecture:

1. **Test Component Independence**
   ```php
   ComponentRegistry::load('ui-components');
   // Should work without loading anything else
   ```

2. **Test Data Isolation**
   ```php
   $originalData = DataProvider::get('properties');
   DataProvider::set('properties', $newData);
   // Should only affect this specific context
   ```

3. **Test View Rendering**
   ```php
   $output = ViewManager::render('admin.dashboard');
   // Should render without side effects
   ```

## Troubleshooting

### If components still break:
1. Check for direct includes (search for `require_once`)
2. Look for global state modifications
3. Verify component dependencies are registered
4. Ensure data is centralized, not scattered

### If views don't render:
1. Make sure framework is initialized
2. Check view path resolution
3. Verify layout is set correctly
4. Ensure data is available in DataProvider

## Benefits of This Architecture

- **No More Scattering**: Changes in one component don't affect others
- **Easier Testing**: Each component can be tested independently
- **Better Performance**: Load only what you need
- **Cleaner Code**: Clear separation of concerns
- **Easier Maintenance**: Centralized data and dependency management

## Quick Reference

| Task | Old Way | New Way |
|------|---------|---------|
| Load Component | `require_once` | `ComponentRegistry::load()` |
| Get Data | Create in view | `DataProvider::get()` |
| Render View | Manual includes | `ViewManager::render()` |
| Set Layout | Include file | `ViewManager::setLayout()` |
| Add Data | Global variables | `DataProvider::set()` |

Follow this guide and your components will stop scattering!
