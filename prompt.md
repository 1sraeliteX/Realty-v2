You are working inside an existing production real estate management codebase.

Your task is to **add a comprehensive list of property types to the property creation/edit forms** and **upgrade all large dropdown fields to include a searchable dropdown with a built-in search bar** so users can easily find options without scrolling.

Follow the **existing architecture, components, styling system, and form handling already used in the project**.
Do not introduce new libraries unless the project already uses them.

---

# Objective

1. Add the full list of property types below to the **Property Type dropdown** used when creating or editing properties.
2. Upgrade the dropdown so users can **search inside the dropdown instead of scrolling through a long list**.
3. Ensure the same dropdown component is reused anywhere **property type selection appears**.
4. Ensure this works in **Admin and Super Admin dashboards**.

---

# Property Types List

Add the following property types as selectable options:

Apartment
Flat
Studio Apartment
Duplex
Triplex
Quadplex
Detached House
Semi-Detached House
Bungalow
Terrace House
Townhouse
Condominium
Penthouse
Loft
Cottage
Villa
Mansion
Mobile Home
Tiny Home
Serviced Apartment
Student Housing
Co-Living Space
Lodge
Self Contain
Mini Flat
Room and Parlor
Apartment Building
Residential Complex
Block of Flats
Hostel
Dormitory
Boarding House
Office Building
Office Space
Office Suite
Co-Working Space
Retail Shop
Shop
Shopping Mall
Strip Mall
Supermarket
Restaurant
Cafe
Bar
Lounge
Hotel
Motel
Guest House
Event Center
Cinema
Bank Building
Clinic
Hospital
Pharmacy
School
Training Center
Warehouse
Factory
Manufacturing Plant
Distribution Center
Cold Storage Facility
Assembly Plant
Industrial Yard
Residential Land
Commercial Land
Industrial Land
Agricultural Land
Farm Land
Ranch Land
Undeveloped Land
Development Site
Estate Plot
Church
Mosque
Temple
Cemetery
Government Building
Military Facility
Prison
Stadium
Sports Complex
Convention Center
Library
Museum
Mixed Use Building
Shop and Apartment
Office and Retail Building
Mixed Use Tower

---

# Searchable Dropdown Requirement

The Property Type dropdown must include:

* A **search input inside the dropdown**
* Instant filtering as the user types
* Keyboard navigation support
* Scrollable result list
* Ability to clear selection

Behavior example:

User clicks Property Type field → dropdown opens
At the top of the dropdown:

```
Search property type...
```

Typing:

```
hos
```

Filters results to:

```
Hostel
Hospital
Hotel
```

---

# Reusable Component

Create a **reusable searchable dropdown component** so it can be reused for other large dropdowns in the future.

The component should support:

* label
* placeholder
* search input
* selectable list
* keyboard navigation
* controlled form value
* validation support
* disabled state

This component should replace the existing dropdown only where large lists exist.

---

# Performance Considerations

* Do not render unnecessary items during filtering
* Debounce search input if needed
* Ensure dropdown remains responsive even with long lists

---

# UX Improvements

The dropdown should include:

* highlighted selected item
* hover states
* empty state when search returns no result

Example:

```
No property type found
```

---

# Form Integration

Ensure the selected property type:

* correctly updates the form state
* validates as a required field
* is submitted with the property creation request
* is saved in the database correctly

---

# Code Quality

Follow existing project conventions:

* reuse existing UI primitives
* maintain consistent styling
* avoid duplicating logic
* keep the dropdown component modular and reusable

---

# Expected Output

Implement:

1. A reusable **SearchableDropdown component**
2. Property Type list integration
3. Updated Property form using the new dropdown
4. Proper filtering behavior
5. Full compatibility with Admin and Super Admin dashboards
