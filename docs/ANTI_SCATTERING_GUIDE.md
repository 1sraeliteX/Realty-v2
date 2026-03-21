# Anti-Scattering Prompt
> Paste this BEFORE every task prompt in your IDE AI

---

## PRE-FLIGHT (Run before writing any code)

Before touching anything, output this checklist:

```
SCOPE CHECK:
- [ ] Task goal (1 sentence): ___
- [ ] Files I WILL modify: ___
- [ ] Files I WILL NOT touch: ___
- [ ] Does this require more than 3 files? YES → STOP and ask. NO → proceed.
```

---

## HARD RULES

1. **One task, one scope.** Only modify files directly required by this task.
2. **No opportunistic refactoring.** Do not improve, reorganize, or clean up code outside the task scope — even if it looks messy.
3. **No new dependencies without asking.** Do not install packages, add imports, or introduce new files unless explicitly requested.
4. **Preserve existing interfaces.** Do not rename functions, change method signatures, or alter return types unless the task explicitly requires it.
5. **Stop at 3+ files.** If the task requires touching more than 3 files, pause and list them — wait for confirmation before proceeding.

---

## LAYER RULES (PHP/Supabase Stack)

| Layer | Responsibility | Must NOT |
|---|---|---|
| View | Presentation only | Call DataProvider, run queries, hold business logic |
| Component | Self-contained UI | Know about layout, modify global state |
| DataProvider | Data access only | Render HTML, know about views |
| ViewManager | Rendering pipeline | Hold data, run business logic |
| ComponentRegistry | Dependency loading | Resolve data, touch views |

**Cross-layer calls that are always wrong:**
- View → direct DB query
- Component → `require_once` another component
- DataProvider → HTML output
- View → modifying `$_SESSION` or `$_GLOBAL`

---

## COMPONENT RULES

```php
// ✅ CORRECT
ComponentRegistry::load('ui-components');
$data = DataProvider::get('properties');
echo ViewManager::render('admin.dashboard', $data);

// ❌ WRONG — triggers scattering
require_once __DIR__ . '/../../components/UIComponents.php';
$properties = [['name' => 'Property 1']]; // mock data in view
include __DIR__ . '/../layout.php';
```

**Every component must:**
- Be loadable in isolation without side effects
- Declare its own dependencies via the registry
- Accept data as parameters, never fetch it internally

---

## POST-TASK CHECKLIST

After completing the task, output:

```
CHANGES MADE:
- Modified: [list each file + one-line reason]
- Created: [list any new files]
- Did NOT touch: [confirm key files were untouched]

LAYER COMPLIANCE:
- [ ] No cross-layer violations
- [ ] No direct require_once used
- [ ] No mock data created in views
- [ ] No global state modified
```

---

## WHEN TO STOP AND ASK

Stop and wait for input if:
- The fix requires changing a shared utility used by 3+ other files
- A component needs a new method that didn't exist before
- You're unsure which layer owns the logic
- Completing the task would require touching the layout, bootstrap, or init files
