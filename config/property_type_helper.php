<?php

/**
 * Property Type Helper
 * 
 * Provides categorization and helper functions for property types
 */

function getPropertyTypeCategories() {
    return [
        'residential' => [
            'apartment', 'flat', 'studio_apartment', 'duplex', 'triplex', 'quadplex',
            'detached_house', 'semi_detached_house', 'bungalow', 'terrace_house', 'townhouse',
            'condominium', 'penthouse', 'loft', 'cottage', 'villa', 'mansion',
            'mobile_home', 'tiny_home', 'serviced_apartment', 'student_housing',
            'co_living_space', 'lodge', 'room_and_parlor', 'mini_flat',
            'apartment_building', 'residential_complex', 'block_of_flats', 'hostel',
            'dormitory', 'boarding_house'
        ],
        'commercial' => [
            'office_building', 'office_space', 'office_suite', 'co_working_space',
            'retail_shop', 'shop', 'shopping_mall', 'strip_mall', 'supermarket',
            'restaurant', 'cafe', 'bar', 'lounge', 'hotel', 'motel',
            'guest_house', 'event_center', 'cinema', 'bank_building', 'clinic',
            'hospital', 'pharmacy', 'school', 'training_center'
        ],
        'industrial' => [
            'warehouse', 'factory', 'manufacturing_plant', 'distribution_center',
            'cold_storage_facility', 'assembly_plant', 'industrial_yard'
        ],
        'land' => [
            'residential_land', 'commercial_land', 'industrial_land', 'agricultural_land',
            'farm_land', 'ranch_land', 'undeveloped_land', 'development_site',
            'estate_plot'
        ],
        'special' => [
            'church', 'mosque', 'temple', 'cemetery', 'government_building',
            'military_facility', 'prison', 'stadium', 'sports_complex',
            'convention_center', 'library', 'museum'
        ],
        'mixed' => [
            'mixed_use_building', 'shop_and_apartment', 'office_and_retail_building',
            'mixed_use_tower'
        ]
    ];
}

function getPropertyCategory($type) {
    $categories = getPropertyTypeCategories();
    
    foreach ($categories as $category => $types) {
        if (in_array($type, $types)) {
            return $category;
        }
    }
    
    return 'mixed'; // Default fallback
}

function getPropertiesByCategory($category) {
    $categories = getPropertyTypeCategories();
    $allTypes = include __DIR__ . '/property_types.php';
    
    if (isset($categories[$category])) {
        $categoryTypes = $categories[$category];
        $filteredTypes = [];
        
        foreach ($allTypes as $type) {
            if (in_array($type['value'], $categoryTypes)) {
                $filteredTypes[] = $type;
            }
        }
        
        return $filteredTypes;
    }
    
    return [];
}

function getAllPropertyTypesWithCategories() {
    $propertyTypes = include __DIR__ . '/property_types.php';
    $categories = getPropertyTypeCategories();
    
    $categorizedTypes = [];
    
    foreach ($propertyTypes as $type) {
        $category = getPropertyCategory($type['value']);
        $categorizedTypes[] = array_merge($type, [
            'category' => $category,
            'category_label' => ucfirst($category)
        ]);
    }
    
    return $categorizedTypes;
}

function getPropertyTypeOptions($category = null) {
    $allTypes = getAllPropertyTypesWithCategories();
    
    if ($category) {
        $allTypes = array_filter($allTypes, function($type) use ($category) {
            return $type['category'] === $category;
        });
    }
    
    return $allTypes;
}

function getCategoryOptions() {
    return [
        '' => 'All Types',
        'residential' => 'Residential',
        'commercial' => 'Commercial', 
        'industrial' => 'Industrial',
        'land' => 'Land',
        'special' => 'Special',
        'mixed' => 'Mixed'
    ];
}
